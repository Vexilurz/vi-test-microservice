<?php

namespace App\Utils;

class Serializer
{
    /**
     * @param ISerialized[] $objects
     * @param array $options
     * @return array
     */
    public static function getSerializedFromArray(array $objects, array $options = []): array {
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getSerialized($options);
        }
        return $result;
    }
}