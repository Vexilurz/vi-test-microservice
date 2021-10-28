<?php

namespace App\Security\RequestChecker;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationRequestChecker extends RequestChecker
{
    /**
     * Must match with AuthController->register() (/register) "name=" in Route annotation
     */
    private const REGISTRATION_ROUTE = 'app_registration';

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($urlGenerator, self::REGISTRATION_ROUTE);
    }
}