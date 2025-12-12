<?php

namespace App\Filament\Actions;

use App\Models\AccessLog;
use Closure;
use Filament\Actions\Action;

class LoggableAction
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
                    AccessLog::create([
                        'user_id' => optional(auth()->user())->id,
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
                    AccessLog::create([
                        'user_id' => optional(auth()->user())->id,
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
     * Convenience method to log an create directly from your own callback.
     */
    public static function logCreate(mixed $record, mixed $data = null, string $name = 'filament.create'): void
    {
        try {
            AccessLog::create([
                'user_id' => optional(auth()->user())->id,
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
     * Convenience method to log an edit directly from your own callback.
     */
    public static function logEdit(mixed $record, mixed $data = null, string $name = 'filament.edit'): void
    {
        try {
            AccessLog::create([
                'user_id' => optional(auth()->user())->id,
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
            AccessLog::create([
                'user_id' => optional(auth()->user())->id,
                'endpoint' => $name,
                'method' => 'DELETE',
                'model_type' => is_object($record) ? get_class($record) : null,
                'model_id' => $record?->getKey() ?? null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'request_payload' => is_object($record) && method_exists($record, 'toArray') ? json_encode($record->toArray()) : null,
                'response_status' => 200,
                'accessed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }
    }

    /**
     * Log bulk deletes. Accepts a Collection, array of models/ids, or array of arrays.
     * Creates one AccessLog entry per deleted record so `model_id` and `model_type` are populated.
     */
    public static function logBulkDelete(mixed $records, string $name = 'filament.bulk.delete'): void
    {
        try {
            $items = [];

            if ($records instanceof \Illuminate\Support\Collection) {
                $items = $records->all();
            } elseif (is_array($records)) {
                $items = $records;
            } else {
                $items = [$records];
            }

            foreach ($items as $item) {
                $model = null;
                $modelId = null;
                $modelType = null;
                $payload = null;

                if (is_object($item) && method_exists($item, 'getKey')) {
                    $model = $item;
                    $modelId = $item->getKey();
                    $modelType = get_class($item);
                    $payload = method_exists($item, 'toArray') ? json_encode($item->toArray()) : null;
                } elseif (is_array($item)) {
                    // array might contain id or model info
                    $modelId = $item['id'] ?? $item['model_id'] ?? null;
                    $modelType = $item['model_type'] ?? null;
                    $payload = json_encode($item);
                } else {
                    // scalar id
                    $modelId = $item;
                }

                try {
                    AccessLog::create([
                        'user_id' => optional(auth()->user())->id,
                        'endpoint' => $name,
                        'method' => 'DELETE',
                        'model_type' => $modelType ?? (is_object($model) ? get_class($model) : null),
                        'model_id' => $modelId ?? ($model?->getKey() ?? null),
                        'ip_address' => request()?->ip(),
                        'user_agent' => request()?->userAgent(),
                        'request_payload' => $payload,
                        'response_status' => 200,
                        'accessed_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    // continue on error for individual items
                }
            }
        } catch (\Throwable $e) {
            // ignore bulk logging errors
        }
    }
}
