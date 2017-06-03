<?php
/**
 * Created by PhpStorm.
 * User: alex1rap
 * Date: 03.06.2017
 * Time: 19:32
 */

namespace RAP\converters;

class ArrayConverter
{
    protected $array;

    /**
     * Converter constructor.
     * @param array $array
     */
    public function __construct($array = [])
    {
        $this->array = $array;
    }

    /**
     * @param array $array
     */
    public function setArray($array)
    {
        $this->array = $array;
    }

    public function asJSON()
    {
        return json_encode($this->array);
    }

    public function asXML()
    {
        return "<?xml version=\"1.1\" encoding=\"UTF-8\" ?>" . $this->toXML($this->array);
    }

    protected function toXML($array = [])
    {
        $result = '';
        foreach($array as $item => $value) {
            if (is_numeric($item)) {
                $item = 'item_' . $item;
            }
            $value = !is_array($value) ? $value : $this->toXML($value);
            $result .= "<{$item}>{$value}</{$item}>";
        }
        return $result;
    }
}