<?php

namespace App\Utils;

interface JsonConverterInterface
{
    public function getJsonArray(array $options = []): array;
}