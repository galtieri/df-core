<?php

namespace DreamFactory\Core\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class Lookup extends BaseSystemLookup
{
    protected $table = 'system_lookup';

    public static function boot()
    {
        parent::boot();

        /** @noinspection PhpUnusedParameterInspection */
        static::saved(
            function (Lookup $lookup){
                \Cache::forget('system_lookups');
            }
        );

        /** @noinspection PhpUnusedParameterInspection */
        static::deleted(
            function (Lookup $lookup){
                \Cache::forget('system_lookups');
            }
        );
    }

    /**
     * Returns system lookups cached, or reads from db if not present.
     * Pass in a key to return a portion/index of the cached data.
     *
     * @param null|string $key
     * @param null        $default
     *
     * @return mixed|null
     */
    public static function getCachedLookups($key = null, $default = null)
    {
        $cacheKey = 'system_lookups';
        try {
            $result = \Cache::remember($cacheKey, \Config::get('df.default_cache_ttl'), function (){
                return Lookup::all()->toArray();
            });

            if (is_null($result)) {
                return $default;
            }
        } catch (ModelNotFoundException $ex) {
            return $default;
        }

        if (is_null($key)) {
            return $result;
        }

        return (isset($result[$key]) ? $result[$key] : $default);
    }

}