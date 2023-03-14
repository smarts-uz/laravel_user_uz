<?php


namespace App\Services;

use App\Models\Content;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

class CustomService
{

    /**
     * Bu method Content modeldan page va keyga qarab textlarni qaytaradi
     * @param $page
     * @param $key
     * @return HigherOrderBuilderProxy|mixed
     */
    public static function getContentText($page, $key): mixed
    {
        $text = app()->getLocale() === 'ru' ? 'text_ru' : 'text_uz';
        return Content::query()->where('page', $page)->where('key', $key)->first()->$text;
    }

    /**
     * Bu method Content modeldan page va keyga qarab rasmlarni qaytaradi
     * @param $page
     * @param $key
     * @return string
     */
    public static function getContentImage($page, $key): string
    {
        $path = Content::query()->where('page', $page)->where('key', $key)->first()->image;
        $path = str_replace('\\', '/', $path);
        return asset('storage/' . $path);
    }

    /**
     * bu method locate uz bolsa uz_Latn qiymatini qaytaradi
     * @return string
     */
    public function getLocale(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'uz') {
            $locale = 'uz_Latn';
        }
        return $locale;
    }

    /**
     * bu method qiymatni cachega saqlab uni qaytaradi
     * @param $key
     * @param $property
     * @param $value
     * @return Repository|mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updateCache($key, $property, $value): mixed
    {
        $data = cache()->get($key);
        $data[$property] = $value;
        cache()->put($key, $data);
        return $data;
    }

    /**
     * telefon raqamni to'g'ri formatda qaytaradi
     * @param $phone
     * @return string
     */
    public function correctPhoneNumber($phone)
    {
        return match (true) {
            strlen($phone) == 12 => '+' . $phone,
            strlen($phone) > 13 => substr($phone, 0, 13),
            default => $phone,
        };
    }

    /**
     *  bu method userning appda qaysi til ekanini aniqlab beradi
     * @param $id
     * @return Repository|mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function cacheLang($id): mixed
    {
        return cache()->get('lang' . $id);
    }

}
