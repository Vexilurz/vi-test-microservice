<?php

namespace App\Security;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationRequestChecker extends RequestChecker
{
    // Must match with AuthController /register name
    private const REGISTRATION_ROUTE = 'app_registration';

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($urlGenerator, self::REGISTRATION_ROUTE);
    }
}