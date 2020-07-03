<?php

namespace Tolkam\Routing\Rule;

use Aura\Router\Route;
use Aura\Router\Rule\RuleInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Rule to match request content type
 */
class AllowContentTypes implements RuleInterface
{
    public function __invoke(ServerRequestInterface $request, Route $route)
    {
        $types = $request->getHeader('Content-Type');
        $allowedTypes = $route->allowedContentTypes;

        // Content-Type header is required
        if ($allowedTypes && empty($types) || array_diff_assoc($allowedTypes, $types)) {
            return false;
        }

        return true;
    }
}
