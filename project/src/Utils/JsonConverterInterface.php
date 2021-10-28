<?php

namespace App\Utils;

interface JsonConverterInterface
{
    public function getJson(array $options = []): array;
}