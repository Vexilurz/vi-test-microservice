<?php

namespace App\Tests\Home;

use App\Tests\VitmBaseWebTestCase;

class HomeTest extends VitmBaseWebTestCase
{
    public function testHome(): void
    {
        $this->checkResponseWithMessage('Hello world!');
    }
}
