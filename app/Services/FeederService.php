<?php

namespace App\Services;

use App\Models\FeederCredential;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Throwable;

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

    public function __construct(array $credentials = [])
    {
        $this->host = $this->credential('feeder_url', $credentials);
        $this->port = $this->credential('feeder_port', $credentials) ?: '8080';
        $this->username = $this->credential('feeder_username', $credentials);
        $this->password = $this->credential('feeder_password', $credentials);
        $this->endpoint = $this->credential('feeder_endpoint', $credentials) ?: 'ws/live2.php';
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

    /**
     * @return array{ok: bool, message: string}
     */
    public function testConnection(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'message' => 'URL, username, dan password Feeder wajib diisi.'];
        }

        try {
            $this->loginFeeder();

            return ['ok' => true, 'message' => "Berhasil terhubung ke Feeder di {$this->baseUrl()}."];
        } catch (Throwable $exception) {
            return ['ok' => false, 'message' => $exception->getMessage()];
        }
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

    /**
     * Ambil semua data dari feeder dengan pagination loop (limit/offset).
     */
    public function getDataAll(string $action, array $params = [], int $chunk = 5000): array
    {
        $all = [];
        $offset = 0;

        while (true) {
            $batch = $this->getData($action, array_merge($params, [
                'limit' => $chunk,
                'offset' => $offset,
            ]));

            $count = count($batch);
            $totalBefore = count($all);
            $all = array_merge($all, $batch);

            // Berhenti jika batch kosong/lebih kecil dari chunk,
            // atau feeder mengabaikan limit/offset (total tidak bertambah).
            if ($count < $chunk || count($all) === $totalBefore) {
                break;
            }

            $offset += $chunk;
        }

        return $all;
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

    private function credential(string $key, array $credentials): string
    {
        if (array_key_exists($key, $credentials)) {
            return (string) ($credentials[$key] ?? '');
        }

        return $this->getDecrypted($key);
    }

    private function getDecrypted(string $key): string
    {
        $encrypted = FeederCredential::where('key_name', $key)->value('key_value');

        if (! $encrypted) {
            return '';
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (Throwable) {
            return '';
        }
    }
}
