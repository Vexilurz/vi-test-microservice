<?php

namespace App\Tests\Home;

use App\Tests\VitmWebTestCase;

class HomeTest extends VitmWebTestCase
{
    public function testHome(): void
    {
        $this->checkResponseWithMessage('Hello world!');
    }
}
