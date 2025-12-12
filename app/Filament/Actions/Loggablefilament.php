<?php

namespace App\Filament\Actions;

use App\Models\ApiAccessLog;
use Closure;
use Filament\Actions\Action;

class Loggablefilament
{
    /**
     * Create an Action that runs the given handler and writes an ApiAccessLog entry.
     *
     * Usage:
     * LoggableAction::make('toggle_status', function ($record) { ... })
     *
     * @param  Closure  $handler  function($record, $data = null)
     */
    public static function make(string $name, Closure $handler, array $options = []): Action
    {
        $action = Action::make($name)
            ->action(function ($record, ?array $data = null) use ($handler, $name) {
                // Execute the handler provided by caller
                $result = $handler($record, $data);

                try {
                    ApiAccessLog::create([
                        'api_user_id' => optional(auth()->user())->id,
                        'endpoint' => 'filament.action.'.$name,
                        'method' => 'POST',
                        'model_type' => is_object($record) ? get_class($record) : null,
                        'model_id' => $record?->getKey() ?? null,
                        'ip_address' => request()?->ip(),
                        'user_agent' => request()?->userAgent(),
                        'request_payload' => filled($data) ? json_encode($data) : null,
                        'response_status' => 200,
                        'accessed_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    // Fallback: don't break the action when logging fails
                }

                return $result;
            });

        // apply simple options
        if (! empty($options['label'])) {
            $action->label($options['label']);
        }

        if (! empty($options['icon'])) {
            $action->icon($options['icon']);
        }

        if (! empty($options['requiresConfirmation'])) {
            $action->requiresConfirmation();
        }

        return $action;
    }

    /**
     * Attach logging to an existing Action (for example EditAction::make()).
     *
     * Usage:
     * LoggableAction::attachTo($editAction, 'edit.user');
     */
    public static function attachTo(Action $action, string $name = 'filament.action'): Action
    {
        try {
            $action->after(function (...$args) use ($name) {
                // Filament may inject different parameters; try to resolve record and data
                $record = null;
                $data = null;

                foreach ($args as $arg) {
                    if (is_object($arg) && method_exists($arg, 'getKey')) {
                        $record = $arg;

                        continue;
                    }

                    if (is_array($arg)) {
                        $data = $arg;

                        continue;
                    }
                }

                try {
                    ApiAccessLog::create([
                        'api_user_id' => optional(auth()->user())->id,
                        'endpoint' => is_string($name) ? $name : 'filament.action',
                        'method' => 'POST',
                        'model_type' => is_object($record) ? get_class($record) : null,
                        'model_id' => $record?->getKey() ?? null,
                        'ip_address' => request()?->ip(),
                        'user_agent' => request()?->userAgent(),
                        'request_payload' => filled($data) ? json_encode($data) : null,
                        'response_status' => 200,
                        'accessed_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    // don't fail the action when logging fails
                }
            });
        } catch (\Throwable $e) {
            // If Action doesn't support ->after() or something goes wrong, ignore
        }

        return $action;
    }

    /**
     * Convenience method to log an edit directly from your own callback.
     */
    public static function logEdit(mixed $record, ?array $data = null, string $name = 'filament.edit'): void
    {
        try {
            ApiAccessLog::create([
                'api_user_id' => optional(auth()->user())->id,
                'endpoint' => $name,
                'method' => 'POST',
                'model_type' => is_object($record) ? get_class($record) : null,
                'model_id' => $record?->getKey() ?? null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'request_payload' => filled($data) ? json_encode($data) : null,
                'response_status' => 200,
                'accessed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }
    }

    /**
     * Convenience method to log a delete directly from your own callback.
     */
    public static function logDelete(mixed $record, string $name = 'filament.delete'): void
    {
        try {
            ApiAccessLog::create([
                'api_user_id' => optional(auth()->user())->id,
                'endpoint' => $name,
                'method' => 'DELETE',
                'model_type' => is_object($record) ? get_class($record) : null,
                'model_id' => $record?->getKey() ?? null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'request_payload' => null,
                'response_status' => 200,
                'accessed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }
    }
}
