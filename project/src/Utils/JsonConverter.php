<?php

namespace App\Utils;

class JsonConverter
{
    /**
     * @param JsonConverterInterface[] $objects
     * @param array $options
     * @return array
     */
    public static function getJsonFromEntitiesArray(array $objects, array $options = []): array
    {
        $result = [];
        foreach ($objects as $object) {
            $result[] = $object->getJson($options);
        }

        return $result;
    }
}