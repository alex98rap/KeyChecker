<?php
/**
 * Created by PhpStorm.
 * User: alex1rap
 * Date: 03.06.2017
 * Time: 15:38
 */

namespace RAP\keys;

use RAP\db\DataBase;

class KeyManager
{
    public $key_id;
    public $key_text;
    public $app_id;
    public $is_activated;
    public $activated_time;

    /**
     * KeyManager constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                if (property_exists($this, $param)) {
                    $this->$param = $value;
                }
            }
        }
    }
/*
 * //не советую использовать данный код, т.к. хакер методом перебора узнает существование ключа активации, сам ключ и идентификатор приложения, активированного данным ключем и сможет активировать свою копию приложения без ее покупки
    public static function findByKey($key)
    {
        $db = new DataBase();
        return new self($db->changeTable('sl_keys')->select(['*'])->andWhere(['key_text' => $key])->one());
    }*/

   /* public static function findByAppId($appId)
    {
        $db = new DataBase();
        return new self($db->changeTable('sl_keys')->select(['*'])->andWhere(['app_id' => $appId])->one());
    }*/

    protected static function findKey($key)
    {
        $db = new DataBase();
        return new self($db->changeTable('sl_key_list')->select(['*'])->andWhere(['key_text' => $key])->one());
    }

    public static function check($app_id, $key_text)
    {
        $db = new DataBase();
        if ($key = $db->changeTable('sl_keys')->select(['*'])->andWhere([
            'app_id' => $app_id,
            'key_text' => $key_text
        ])->one()
        ) {
            if ($key['key_text'] == $key_text) {
                return new self($key);
            }
        }
        return false;
    }

    public static function register($key_text)
    {
        $db = new DataBase();
        $key = self::findKey($key_text);
        if ($key !== false && $key->key_id > 0 && $key->is_activated == 0) {
            if (!$db->changeTable('sl_key_list')->andWhere([
                'key_id' => $key->key_id,
                'key_text' => $key->key_text
            ])->update([
                'is_activated' => 1
            ])) return false;
            $app_id = md5($key_text . time());
            if (!$db->changeTable('sl_keys')->add([
                'key_text' => $key_text,
                'app_id' => $app_id,
                'activated_time' => time()
            ])) return false;
            $app = self::check($app_id, $key_text);
            if (empty($app)) {
                return false;
            }
            return new self([
                'key_id' => $app->key_id,
                'key_text' => $app->key_text,
                'app_id' => $app->app_id,
                'is_activated' => 1,
                'activated_time' => $app->activated_time
            ]);
        }
        return false;
    }
}
