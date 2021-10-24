<?php

namespace App\Utils;

class Serializer
{
    //TODO: mark that $objects are array of doctrine entities with getSerialized() method
    public static function getSerializedFromArray($objects, array $options = []): array {
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getSerialized($options);
        }
        return $result;
    }
}