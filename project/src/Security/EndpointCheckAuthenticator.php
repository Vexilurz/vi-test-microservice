<?php

namespace App\Security;

use App\Security\RequestChecker\RequestCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

abstract class EndpointCheckAuthenticator extends AbstractAuthenticator
{
    private RequestCheckerInterface $requestChecker;

    protected function __construct(RequestCheckerInterface $requestChecker)
    {
        $this->requestChecker = $requestChecker;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $this->requestChecker->isEndpointMatch($request);
    }
}