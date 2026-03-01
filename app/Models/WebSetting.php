<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    public const KEY_HOME_HERO_BACKGROUND_IMAGE = 'home.hero_background_image';

    public const KEY_CONTACT_EMAIL = 'contact.email';
    public const KEY_CONTACT_PHONE = 'contact.phone';
    public const KEY_CONTACT_WHATSAPP = 'contact.whatsapp';

    // About Us (CMS)
    public const KEY_ABOUT_TAG = 'about.tag';
    public const KEY_ABOUT_HEADLINE = 'about.headline';
    public const KEY_ABOUT_SUBHEADLINE = 'about.subheadline';
    /**
     * Multiline text; each line becomes a paragraph.
     */
    public const KEY_ABOUT_LEAD = 'about.lead';

    // About Us image (right card)
    public const KEY_ABOUT_IMAGE = 'about.image';
    public const KEY_ABOUT_IMAGE_ALT = 'about.image_alt';

    // Badge text on image
    public const KEY_ABOUT_BADGE = 'about.badge';
    public const KEY_ABOUT_BADGE_TITLE = 'about.badge_title';
    public const KEY_ABOUT_BADGE_DESC = 'about.badge_desc';

    // Stats
    public const KEY_ABOUT_STAT_1_VALUE = 'about.stat_1.value';
    public const KEY_ABOUT_STAT_1_LABEL = 'about.stat_1.label';
    public const KEY_ABOUT_STAT_2_VALUE = 'about.stat_2.value';
    public const KEY_ABOUT_STAT_2_LABEL = 'about.stat_2.label';
    public const KEY_ABOUT_STAT_3_VALUE = 'about.stat_3.value';
    public const KEY_ABOUT_STAT_3_LABEL = 'about.stat_3.label';


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
