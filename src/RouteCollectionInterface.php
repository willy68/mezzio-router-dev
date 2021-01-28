<?php

/**
 * https://github.com/thephpleague/route
 */

declare(strict_types=1);

namespace Mezzio\Router;

interface RouteCollectionInterface
{

    /**
     * Add a route that responds to GET HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function get(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to POST HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function post(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to PUT HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function put(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to PATCH HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function patch(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to DELETE HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function delete(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to HEAD HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     * @return Route
     */
    public function head(string $uri, $callable, ?string $name = null): Route;

    /**
     * Add a route that responds to OPTIONS HTTP method
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     *
     * @return Route
     */
    public function options(string $uri, $callable, ?string $name = null): Route;
}
