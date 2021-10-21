<?php

namespace App\Security\RequestChecker;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class RequestChecker implements RequestCheckerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private string $routeName;

    protected function __construct(UrlGeneratorInterface $urlGenerator, string $routeName)
    {
        $this->urlGenerator = $urlGenerator;
        $this->routeName = $routeName;
    }

    public function isEndpointMatch(Request $request): bool {
        return $request->isMethod('POST') && $this->getLoginUrl($this->routeName) === $request->getPathInfo();
    }

    private function getLoginUrl(string $routeName): string
    {
        return $this->urlGenerator->generate($routeName);
    }
}