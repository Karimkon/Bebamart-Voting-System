<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'loggable_type',
        'loggable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create a log entry for creating a model
     */
    public static function logCreated($user, $module, $model, $description = null)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => 'created',
            'module' => $module,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'description' => $description ?? "Created new {$module}",
            'new_values' => $model->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Create a log entry for updating a model
     */
    public static function logUpdated($user, $module, $model, $oldValues, $description = null)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => 'updated',
            'module' => $module,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'description' => $description ?? "Updated {$module}",
            'old_values' => $oldValues,
            'new_values' => $model->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Create a log entry for deleting a model
     */
    public static function logDeleted($user, $module, $model, $description = null)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => 'deleted',
            'module' => $module,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'description' => $description ?? "Deleted {$module}",
            'old_values' => $model->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }
}
