<?php

/**
 * @see       https://github.com/mezzio/mezzio-router for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-router/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-router/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Router;

/**
 * Aggregate routes for the router.
 *
 * This class provides * methods for creating path+HTTP method-based routes and
 * injecting them into the router:
 *
 * - get
 * - post
 * - put
 * - patch
 * - delete
 * - head
 * - options
 * - any
 *
 * A general `route()` method allows specifying multiple request methods and/or
 * arbitrary request methods when creating a path-based route.
 *
 * Internally, the class performs some checks for duplicate routes when
 * attaching via one of the exposed methods, and will raise an exception when a
 * collision occurs.
 */
class RouteCollector implements RouteCollectionInterface
{
    /** @var RouterInterface */
    protected $router;

    /** @var bool */
    protected $detectDuplicates = true;

    /**
     * List of all routes registered directly with the application.
     *
     * @var RouteInterface[]
     */
    private $routes = [];

    /** @var null|DuplicateRouteDetector */
    private $duplicateRouteDetector;

    public function __construct(RouterInterface $router, bool $detectDuplicates = true)
    {
        $this->router           = $router;
        $this->detectDuplicates = $detectDuplicates;
    }

    /**
     * Add a route for the route middleware to match.
     *
     * Accepts a combination of a path and callback, and optionally the HTTP methods allowed.
     *
     * @param null|array  $methods HTTP method to accept; null indicates any.
     * @param null|string $name The name of the route.
     * @throws Exception\DuplicateRouteException If specification represents an existing route.
     */
    public function route(
        string $path,
        $callback,
        ?string $name = null,
        ?array $methods = null
    ): RouteInterface {
        $methods = $methods ?? Route::HTTP_METHOD_ANY;
        $route   = new Route($path, $callback, $name, $methods);
        $this->detectDuplicate($route);
        $this->router->addRoute($route);
        $this->routes[$route->getName()] = $route;

        return $route;
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function get(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, ['GET']);
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function post(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, ['POST']);
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function put(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, ['PUT']);
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function patch(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, ['PATCH']);
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function delete(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, ['DELETE']);
    }

    /**
     * @param null|string $name The name of the route.
     */
    public function any(string $path, $callback, ?string $name = null): RouteInterface
    {
        return $this->route($path, $callback, $name, null);
    }

    /**
     * Add a route that responds to HEAD HTTP method
     *
     * @param string $uri
     * @param callable|string $callable
     * @param string|null $name
     * @return RouteInterface
     */
    public function head(string $uri, $callable, ?string $name = null): RouteInterface
    {
        return $this->route($uri, $callable, $name, ['HEAD']);
    }

    /**
     * Add a route that responds to OPTIONS HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return RouteInterface
     */
    public function options(string $uri, $callable, ?string $name = null): RouteInterface
    {
        return $this->route($uri, $callable, $name, ['OPTIONS']);
    }

    /**
     * Retrieve all directly registered routes with the application.
     *
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Retrieve Route by name
     *
     * @param string $name
     * @return RouteInterface|null
     */
    public function getRouteName(string $name): ?RouteInterface
    {
        return $this->routes[$name] ?? null;
    }

    private function detectDuplicate(RouteInterface $route): void
    {
        if ($this->detectDuplicates && !$this->duplicateRouteDetector) {
            $this->duplicateRouteDetector = new DuplicateRouteDetector();
        }

        if ($this->duplicateRouteDetector) {
            $this->duplicateRouteDetector->detectDuplicate($route);
            return;
        }
    }
}
