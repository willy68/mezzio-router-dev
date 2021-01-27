<?php

namespace Mezzio\Router;

use Mezzio\Router\Middleware\Stack\MiddlewareAwareStackTrait;


/**
 * Ex:
 * ```
 * $router->group('/admin', function (RouteGroup $route) {
 * $route->addRoute('/acme/route1', 'AcmeController::actionOne', 'route1', [GET]);
 * $route->addRoute('/acme/route2', 'AcmeController::actionTwo', 'route2', [GET])->setScheme('https');
 * $route->addRoute('/acme/route3', 'AcmeController::actionThree', 'route3', [GET]);
 * })
 * ->middleware(Middleware::class);
 * ```
 *
 */
class RouteGroup
{
    use MiddlewareAwareStackTrait;
    use RouteCollectionTrait;

    /**
     * Route prefix for this group
     *
     * @var string
     */
    private $prefix;

    /**
     * Called by router
     *
     * @var callable
     */
    private $callable;

    /**
     * Router
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * Construct
     *
     * @param string $prefix
     * @param callable $callable
     * @param RouterInterface $router
     */
    public function __construct(string $prefix, callable $callable, RouterInterface $router)
    {
        $this->prefix = $prefix;
        $this->callable = $callable;
        $this->router = $router;
    }

    /**
     * Run $callable
     *
     * @return void
     */
    public function __invoke()
    {
        ($this->callable)($this);
    }

    /**
     * Add route
     *
     * @param string $uri
     * @param string|callable $callable
     * @param string|null $name
     * @param array|null $method
     * @return Route
     */
    public function addRoute(string $uri, $callable, ?string $name = null, ?array $method = null): Route
    {
        $path  = ($uri === '/') ? $this->prefix : $this->prefix . sprintf('/%s', ltrim($uri, '/'));
        if ($name === null) {
            $name = ($method === null) ? $this->prefix . $uri : $this->prefix . $uri . '^' . join(':', $method);
        }
        $route = $this->router->addRoute(new Route($path, $callable, $name, $method));

        $route->setParentGroup($this);
        return $route;
    }

    /**
     * Undocumented function
     *
     * @param string|callable $callable
     * @param string $prefixName
     * @return self
     */
    public function crud($callable, string $prefixName): self
    {
        $this->get("/", $callable . '::index', "$prefixName.index");
        $this->get("/new", $callable . '::create', "$prefixName.create");
        $this->post("/new", $callable . '::create');
        $this->get("/{id:\d+}", $callable . '::edit', "$prefixName.edit");
        $this->post("/{id:\d+}", $callable . '::edit');
        $this->delete("/{id:\d+}", $callable . '::delete', "$prefixName.delete");
        return $this;
    }

    /**
     * Get the value of prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
