<?php declare(strict_types=1);

namespace Tolkam\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tolkam\Routing\Util\Route;
use Tolkam\Utils\Url;

class RoutingHelper
{
    public function __construct(
        private readonly ServerRequestInterface $request,
        private readonly RouterAdapterInterface $adapter
    ) {
    }

    /**
     * Gets all defined routes
     *
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->adapter->getRoutes();
    }

    /**
     * Gets route by name or get current one if name is not provided
     *
     * @param string|null $name
     *
     * @return RouteInterface
     */
    public function getRoute(string $name = null): RouteInterface
    {
        return $this->adapter->getRoute($name);
    }

    /**
     * Gets route attribute value
     *
     * @param string|null $name
     * @param string      $attribute
     * @param             $default
     *
     * @return string|null
     */
    public function getRouteAttribute(?string $name, string $attribute, $default = null): ?string
    {
        $attributes = $this->getRoute($name)->getAttributes();

        return $attributes[$attribute] ?? $default;
    }

    /**
     * Gets current route name
     *
     * @return string
     */
    public function currentRouteName(): string
    {
        return $this->getRoute()->getName();
    }

    /**
     * Gets current route url
     *
     * @param string|null $name
     * @param array       $attrs
     * @param bool|null   $absolute
     * @param bool|null   $preserveReturnTo
     *
     * @return string
     */
    public function getRouteUrl(
        string $name = null,
        array $attrs = [],
        bool $absolute = false,
        bool $preserveReturnTo = true
    ): string {
        if (!$name) {
            $currentRoute = $this->getRoute();
            $name = $currentRoute->getName();
            $attrs = array_replace($currentRoute->getAttributes(), $attrs);
        }

        $url = $this->adapter->generate($name, $attrs);

        if ($absolute) {
            $url = Url::toAbsolute($url, $this->getHost(), $this->getScheme());
        }

        // preserve return-to parameter between urls
        if ($preserveReturnTo && ($returnTo = Route::getReturnTo($this->request))) {
            $url = Route::addReturnTo($url, $returnTo);
        }

        return $url;
    }

    /**
     * Adds redirect header to response
     *
     * @param ResponseInterface $response
     * @param string            $redirectRouteName
     * @param bool              $returnToRouteName
     *
     * @return ResponseInterface
     */
    public function withRedirect(
        ResponseInterface $response,
        string $redirectRouteName,
        bool $returnToRouteName = false
    ): ResponseInterface {
        $location = $this->getRouteUrl($redirectRouteName);

        if (
            $returnToRouteName !== false
            && (is_null($returnToRouteName) || is_string($returnToRouteName))
        ) {
            $returnTo = $this->getRouteUrl($returnToRouteName);
        }
        else {
            $returnTo = Route::getReturnTo($this->request);
        }

        if ($returnTo !== null) {
            $location = Route::addReturnTo($location, $returnTo);
        }

        return $response->withStatus(302)->withHeader('Location', $location);
    }

    /**
     * Gets current request URI scheme
     *
     * @return string
     */
    public function getScheme(): string
    {
        // get from uri or from globals when uri is not available (CLI)
        $fallback = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $fallback .= 's';
        }

        return $this->request->getUri()->getScheme() ?: $fallback;
    }

    /**
     * Gets current request URI host
     *
     * @return string
     */
    public function getHost(): string
    {
        // get from uri or from globals when uri is not available (CLI)
        return $this->request->getUri()->getHost()
            ?: ($_SERVER['HTTP_HOST'] ?? '');
    }

    /**
     * Gets current request URI path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->request->getUri()->getPath();
    }
}
