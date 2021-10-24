<?php

namespace App\Utils;

class Serializer
{
    public static function getSerializedFromArray(array $objects): array {
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getSerialized();
        }
        return $result;
    }
}