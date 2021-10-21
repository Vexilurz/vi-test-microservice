<?php

namespace App\Security\RequestChecker;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginRequestChecker extends RequestChecker
{
    // Must match with AuthController /login name
    private const LOGIN_ROUTE = 'app_login';

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($urlGenerator, self::LOGIN_ROUTE);
    }
}