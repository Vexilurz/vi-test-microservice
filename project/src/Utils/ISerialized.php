<?php

namespace App\Utils;

interface ISerialized
{
    public function getSerialized(array $options = []): array;
}