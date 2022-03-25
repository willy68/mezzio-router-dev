<?php

/**
 * https://github.com/thephpleague/route
 */

declare(strict_types=1);

namespace Mezzio\Router;

trait RouteCollectionTrait
{
    /**
     * Add a route to the collection
     */
    abstract public function addRoute(Route $route): Route;

    /**
     * Add a route to the collection
     *
     * @param string|callable $callable
     * @param array|null $method
     */
    public function route(string $uri, $callable, ?string $name = null, ?array $method = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, $method));
    }

    /**
     * Add a route that responds to GET HTTP method
     *
     * @param string|callable $callable
     */
    public function get(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['GET']));
    }

    /**
     * Add a route that responds to POST HTTP method
     *
     * @param string|callable $callable
     */
    public function post(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['POST']));
    }

    /**
     * Add a route that responds to PUT HTTP method
     *
     * @param string|callable $callable
     */
    public function put(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['PUT']));
    }

    /**
     * Add a route that responds to PATCH HTTP method
     *
     * @param string|callable $callable
     */
    public function patch(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['PATCH']));
    }

    /**
     * Add a route that responds to DELETE HTTP method
     *
     * @param string|callable $callable
     */
    public function delete(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['DELETE']));
    }

    /**
     * Add a route that responds to HEAD HTTP method
     *
     * @param callable|string $callable
     */
    public function head(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['HEAD']));
    }

    /**
     * Add a route that responds to OPTIONS HTTP method
     *
     * @param string|callable $callable
     */
    public function options(string $uri, $callable, ?string $name = null): Route
    {
        return $this->addRoute(new Route($uri, $callable, $name, ['OPTIONS']));
    }
}
