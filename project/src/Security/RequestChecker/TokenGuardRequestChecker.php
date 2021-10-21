<?php

namespace App\Security\RequestChecker;

use Symfony\Component\HttpFoundation\Request;

class TokenGuardRequestChecker implements RequestCheckerInterface
{
    private array $requestCheckers;

    public function __construct(LoginRequestChecker $loginRequestChecker,
                                RegistrationRequestChecker $registrationRequestChecker)
    {
        $this->requestCheckers[] = $loginRequestChecker;
        $this->requestCheckers[] = $registrationRequestChecker;
    }

    public function isEndpointMatch(Request $request): bool {
        foreach ($this->requestCheckers as $requestChecker) {
            if ($requestChecker->isEndpointMatch($request)) {
                return false;
            }
        }
        return true;
    }
}