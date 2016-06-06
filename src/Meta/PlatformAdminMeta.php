<?php

namespace Tokenly\PlatformAdmin\Meta;

use DB;
use Exception;
use Illuminate\Support\Facades\Log;

class PlatformAdminMeta
{
    static $TABLE_NAME = 'platform_admin_meta';
    
    public static function get($key, $default=null) {
        try {
            $result = DB::table(self::$TABLE_NAME)->select('meta_value')->where('meta_key', '=', $key)->first();
            if (!$result) { return $default; }
            return json_decode($result->meta_value, true);
        } catch (Exception $e) {
            Log::error("PlatformAdminMeta ".get_class($e)." at ".$e->getFile().":".$e->getLine().": ".$e->getMessage()."\n".$e->getTraceAsString());
            return null;
        }
    }

    public static function getMulti($keys) {
        try {
            $results = DB::table(self::$TABLE_NAME)->select(['meta_key','meta_value'])->whereIn('meta_key', $keys)->get();
            $out = array_fill_keys($keys, null);
            foreach($results as $result) {
                $out[$result->meta_key] = json_decode($result->meta_value, true);
            }
            return $out;
        } catch (Exception $e) {
            Log::error("PlatformAdminMeta ".get_class($e)." at ".$e->getFile().":".$e->getLine().": ".$e->getMessage()."\n".$e->getTraceAsString());
            return null;
        }
    }

    public static function set($key, $value) {
        if (strlen($key) > 255) { throw new Exception("Key must be 255 charavters or less", 1); }

        return DB::transaction(function() use ($key, $value) {
            $existing_record = DB::table(self::$TABLE_NAME)->select('meta_key')->where('meta_key', '=', $key)->lockForUpdate()->first();

            // update
            if ($existing_record) {
                return DB::table(self::$TABLE_NAME)->where('meta_key', '=', $key)->update([
                    'meta_value' => json_encode($value),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }

            // create
            return DB::table(self::$TABLE_NAME)->insert([
                'meta_key'   => $key,
                'meta_value' => json_encode($value),
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        });
    }

    public static function setMulti($data) {
        foreach($data as $data_key => $data_val) {
            self::set($data_key, $data_val);
        }
    }

    public static function delete($key) {
        return DB::table(self::$TABLE_NAME)->where('meta_key', '=', $key)->delete();
    }

    public static function exists($key) {
        $result = DB::table(self::$TABLE_NAME)->select('meta_key')->where('meta_key', '=', $key)->first();
        return !!$result;
    }
    
    
}
