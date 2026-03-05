<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    // Site Identity
    public const KEY_SITE_NAME = 'site.name';
    public const KEY_SITE_TAGLINE = 'site.tagline';
    public const KEY_SITE_LOGO = 'site.logo';
    public const KEY_LOGIN_SIDE_IMAGE = 'site.login_side_image';

    public const KEY_HOME_HERO_BACKGROUND_IMAGE = 'home.hero_background_image';


    public const KEY_CONTACT_EMAIL = 'contact.email';
    public const KEY_CONTACT_PHONE = 'contact.phone';
    public const KEY_CONTACT_WHATSAPP = 'contact.whatsapp';

    // Footer
    public const KEY_FOOTER_TITLE = 'footer.title';
    public const KEY_FOOTER_DESCRIPTION = 'footer.description';
    public const KEY_FOOTER_COPYRIGHT = 'footer.copyright';
    /**
     * Multiline text; each line becomes a payment method label.
     */
    public const KEY_FOOTER_PAYMENT_METHODS = 'footer.payment_methods';

    // Social media
    public const KEY_SOCIAL_INSTAGRAM = 'social.instagram';
    public const KEY_SOCIAL_FACEBOOK = 'social.facebook';
    public const KEY_SOCIAL_TIKTOK = 'social.tiktok';
    public const KEY_SOCIAL_YOUTUBE = 'social.youtube';

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

    // About Us - Vision
    public const KEY_ABOUT_VISION_TAG = 'about.vision.tag';
    public const KEY_ABOUT_VISION_TITLE = 'about.vision.title';
    public const KEY_ABOUT_VISION_BODY = 'about.vision.body';

    // About Us - Mission
    public const KEY_ABOUT_MISSION_TAG = 'about.mission.tag';
    public const KEY_ABOUT_MISSION_TITLE = 'about.mission.title';
    public const KEY_ABOUT_MISSION_BODY = 'about.mission.body';

    // About Us - Values
    public const KEY_ABOUT_VALUES_TAG = 'about.values.tag';
    public const KEY_ABOUT_VALUES_TITLE = 'about.values.title';
    public const KEY_ABOUT_VALUES_DESC = 'about.values.desc';
    public const KEY_ABOUT_VALUES_ITEM_1_TITLE = 'about.values.item_1.title';
    public const KEY_ABOUT_VALUES_ITEM_1_DESC = 'about.values.item_1.desc';
    public const KEY_ABOUT_VALUES_ITEM_2_TITLE = 'about.values.item_2.title';
    public const KEY_ABOUT_VALUES_ITEM_2_DESC = 'about.values.item_2.desc';
    public const KEY_ABOUT_VALUES_ITEM_3_TITLE = 'about.values.item_3.title';
    public const KEY_ABOUT_VALUES_ITEM_3_DESC = 'about.values.item_3.desc';

    // About Us - Highlights
    public const KEY_ABOUT_HIGHLIGHTS_1_TITLE = 'about.highlights_1.title';
    public const KEY_ABOUT_HIGHLIGHTS_1_DESC = 'about.highlights_1.desc';
    public const KEY_ABOUT_HIGHLIGHTS_2_TITLE = 'about.highlights_2.title';
    public const KEY_ABOUT_HIGHLIGHTS_2_DESC = 'about.highlights_2.desc';

    // Car Rental (CMS)
    public const KEY_RENTAL_PAGE_TITLE = 'rental.page.title';
    public const KEY_RENTAL_PAGE_META = 'rental.page.meta';
    public const KEY_RENTAL_PAGE_KEYWORDS = 'rental.page.keywords';
    public const KEY_RENTAL_HERO_TAG = 'rental.hero.tag';
    public const KEY_RENTAL_HERO_TITLE = 'rental.hero.title';
    public const KEY_RENTAL_HERO_DESC = 'rental.hero.desc';
    public const KEY_RENTAL_CTA_TITLE = 'rental.cta.title';
    public const KEY_RENTAL_CTA_DESC = 'rental.cta.desc';
    public const KEY_RENTAL_CTA_BUTTON = 'rental.cta.button';


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
