<?php


namespace App\Services;

use App\Models\Content;

class CustomService
{

    public static function getContentText($page, $key)
    {
        $text = app()->getLocale() === 'ru' ? 'text_ru' : 'text_uz';
        return Content::query()->where('page', $page)->where('key', $key)->first()->$text;
    }

    public static function getContentImage($page, $key): string
    {
        $path = Content::query()->where('page', $page)->where('key', $key)->first()->image;
        $path = str_replace('\\', '/', $path);
        return asset('storage/' . $path);
    }

    public function getLocale(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'uz') {
            $locale = 'uz_Latn';
        }
        return $locale;
    }

    public function updateCache($key, $property, $value)
    {
        $data = cache()->get($key);
        $data[$property] = $value;
        cache()->put($key, $data);
        return $data;
    }

    public function correctPhoneNumber($phone)
    {
        return match (true) {
            strlen($phone) == 12 => '+' . $phone,
            strlen($phone) > 13 => substr($phone, 0, 13),
            default => $phone,
        };
    }

    public function cacheLang($id)
    {
        return cache()->get('lang' . $id);
    }

}
