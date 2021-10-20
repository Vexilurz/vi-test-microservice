<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testLogin(): void
    {
        $response = static::createClient()->request('POST', '/login',
            ['body'=>['email'=>'user@example.com','password'=>'123456']]);

        $this->assertResponseIsSuccessful();

//        $this->assert(["message"=>"login success"]);
    }
}
