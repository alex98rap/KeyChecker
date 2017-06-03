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
    public $app_id;
    public $key_text;
    public $activated_time;
    public $is_activated;

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

    public static function findKey($key)
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
}
