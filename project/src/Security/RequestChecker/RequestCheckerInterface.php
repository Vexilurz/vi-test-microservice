<?php

namespace App\Security\RequestChecker;

use Symfony\Component\HttpFoundation\Request;

interface RequestCheckerInterface {
    public function isEndpointMatch(Request $request): bool;
}