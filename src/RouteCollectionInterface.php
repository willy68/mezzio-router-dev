<?php

/**
 * https://github.com/thephpleague/route
 */

declare(strict_types=1);

namespace Mezzio\Router;

interface RouteCollectionInterface
{
    /**
     * Add a route to the collection.
     */
    public function addRoute(Route $route): Route;

    /**
     * Add a route that responds to GET HTTP method
     *
     * @param string|callable $callable
     */
    public function get(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to POST HTTP method
     *
     * @param string|callable $callable
     */
    public function post(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to PUT HTTP method
     *
     * @param string|callable $callable
     */
    public function put(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to PATCH HTTP method
     *
     * @param string|callable $callable
     */
    public function patch(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to DELETE HTTP method
     *
     * @param string|callable $callable
     */
    public function delete(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to HEAD HTTP method
     *
     * @param string|callable $callable
     */
    public function head(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to OPTIONS HTTP method
     *
     * @param string|callable $callable
     */
    public function options(string $uri, $callable, ?string $name = null): Route;
}
