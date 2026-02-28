<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    public const KEY_HOME_HERO_BACKGROUND_IMAGE = 'home.hero_background_image';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = static::query()->where('key', $key)->value('value');

            return $value !== null ? $value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        try {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        } catch (\Throwable) {
            // ignore (e.g. table not migrated yet)
        }
    }

    public static function forget(string $key): void
    {
        try {
            static::query()->where('key', $key)->delete();
        } catch (\Throwable) {
            // ignore
        }
    }
}
