<?php

function getLocale()
{
    $locale = app()->getLocale();
    if ($locale === 'uz') {
        $locale = 'uz_Latn';
    }
    return $locale;
}

function getContentText($page, $key)
{
    $text = app()->getLocale() === 'ru' ? 'text_ru' : 'text_uz';
    return \App\Models\Content::query()->where('page', $page)->where('key', $key)->first()->$text;
}

function getContentImage($page, $key): string
{
    $path = \App\Models\Content::query()->where('page', $page)->where('key', $key)->first()->image;
    $path = str_replace('\\', '/', $path);
    return asset('storage/' . $path);
}

function updateCache($key, $property, $value)
{
    $data = cache()->get($key);
    $data[$property] = $value;
    cache()->put($key, $data);
    return $data;
}

function correctPhoneNumber($phone)
{
    return match (true) {
        strlen($phone) == 12 => '+' . $phone,
        strlen($phone) > 13 => substr($phone, 0, 13),
        default => $phone,
    };
}

function cacheLang($id)
{
    return cache()->get('lang' . $id);
}

