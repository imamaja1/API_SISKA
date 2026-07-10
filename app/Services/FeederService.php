<?php

namespace App\Services;

use App\Models\FeederCredential;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class FeederService
{
    private string $host;

    private string $port;

    private string $username;

    private string $password;

    private string $endpoint;

    private int $timeout;

    private int $retry;

    private const TOKEN_KEY = 'feeder_token';

    private const TOKEN_EXPIRY = 3600;

    public function __construct()
    {
        $this->host = $this->getDecrypted('feeder_url');
        $this->port = $this->getDecrypted('feeder_port') ?: '8080';
        $this->username = $this->getDecrypted('feeder_username');
        $this->password = $this->getDecrypted('feeder_password');
        $this->endpoint = $this->getDecrypted('feeder_endpoint') ?: 'ws/live2.php';
        $this->timeout = (int) config('feeder.timeout', 30);
        $this->retry = (int) config('feeder.retry', 3);
    }

    private function baseUrl(): string
    {
        return "{$this->host}:{$this->port}/{$this->endpoint}";
    }

    public function isConfigured(): bool
    {
        return $this->host !== '' && $this->username !== '' && $this->password !== '';
    }

    public function getToken(): string
    {
        $cached = $this->getCachedToken();
        if ($cached !== null) {
            return $cached;
        }

        $token = $this->loginFeeder();
        $this->saveToken($token);

        return $token;
    }

    public function getData(string $action, array $params = [], int $retryCount = 0): array
    {
        $token = $this->getToken();

        $payload = array_merge([
            'act' => "Get{$action}",
            'token' => $token,
        ], $params);

        $response = Http::timeout($this->timeout)
            ->retry($this->retry, 1000)
            ->post($this->baseUrl(), $payload);

        $data = $response->json();

        $errcode = $data['error_code'] ?? -1;
        $errdesc = $data['error_desc'] ?? '';

        // Token expired atau invalid -> hapus cache & retry sekali (max 1x)
        if ($errcode !== 0 && $this->isTokenError($errcode, $errdesc) && $retryCount === 0) {
            $this->clearToken();

            return $this->getData($action, $params, 1);
        }

        if ($errcode !== 0) {
            throw new Exception("Feeder getData({$action}) gagal: ({$errcode}) {$errdesc}");
        }

        return $data['data'] ?? [];
    }

    public function getDictionary(): array
    {
        $token = $this->getToken();

        $response = Http::timeout($this->timeout)
            ->post($this->baseUrl(), [
                'act' => 'GetDictionary',
                'token' => $token,
                'fungsi' => '',
            ]);

        $data = $response->json();

        if (! $data || ($data['error_code'] ?? -1) !== 0) {
            throw new Exception('Feeder getDictionary gagal: '.($data['error_desc'] ?? 'response kosong'));
        }

        return $data['data'] ?? [];
    }

    private function loginFeeder(): string
    {
        $response = Http::timeout($this->timeout)
            ->retry($this->retry, 1000)
            ->post($this->baseUrl(), [
                'act' => 'GetToken',
                'username' => $this->username,
                'password' => $this->password,
            ]);

        $data = $response->json();

        if (! $data || ($data['error_code'] ?? -1) !== 0) {
            throw new Exception('Feeder login gagal: '.($data['error_desc'] ?? 'response kosong'));
        }

        // data selalu array: { "token": "..." }
        return $data['data']['token'] ?? throw new Exception('Token tidak ditemukan di response Feeder GetToken');
    }

    private function getCachedToken(): ?string
    {
        $encrypted = FeederCredential::where('key_name', self::TOKEN_KEY)->value('key_value');

        if (! $encrypted) {
            return null;
        }

        try {
            $payload = json_decode(Crypt::decryptString($encrypted), true);

            if (! $payload || ! isset($payload['token'], $payload['expires_at'])) {
                return null;
            }

            if (now()->timestamp >= $payload['expires_at']) {
                return null;
            }

            return $payload['token'];
        } catch (Exception) {
            return null;
        }
    }

    private function saveToken(string $token): void
    {
        FeederCredential::updateOrCreate(
            ['key_name' => self::TOKEN_KEY],
            [
                'key_value' => Crypt::encryptString(json_encode([
                    'token' => $token,
                    'expires_at' => now()->addSeconds(self::TOKEN_EXPIRY)->timestamp,
                ])),
                'description' => 'Bearer Token Feeder (auto-refresh)',
            ]
        );
    }

    private function clearToken(): void
    {
        FeederCredential::where('key_name', self::TOKEN_KEY)->delete();
    }

    private function isTokenError(int $code, string $desc): bool
    {
        // Feeder mengembalikan error tertentu saat token invalid/expired
        $tokenErrors = [101, 102, 103, 104];

        if (in_array($code, $tokenErrors, true)) {
            return true;
        }

        $keywords = ['token', 'session', 'auth', 'login'];
        foreach ($keywords as $kw) {
            if (stripos($desc, $kw) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getDecrypted(string $key): string
    {
        $encrypted = FeederCredential::where('key_name', $key)->value('key_value');

        if (! $encrypted) {
            return '';
        }

        return Crypt::decryptString($encrypted);
    }
}
