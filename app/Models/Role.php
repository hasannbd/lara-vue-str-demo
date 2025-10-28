<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // set created_by_id only on creation
            if (auth()->check()) {
                $model->created_by_id = auth()->id();
            }
            // if you want to override created_at manually
            $model->created_at = now();
        });
        static::updating(function ($model) {
            // set created_by_id only on creation
            if (auth()->check()) {
                $model->updated_by_id = auth()->id();
            }
            // if you want to override created_at manually
            $model->updated_at = now();
        });
    }
}
