<?php

/**
 * https://github.com/thephpleague/route
 */

declare(strict_types=1);

namespace Mezzio\Router\Middleware\Stack;

use Mezzio\Router\Middleware\RoutePrefixMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

use function array_shift;
use function array_unshift;
use function is_string;

trait MiddlewareAwareStackTrait
{
    /** @var array */
    protected $middleware = [];

    /**
     * Add middleware
     *
     * @param string|MiddlewareInterface $middleware
     */
    public function middleware($middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Add middlewares array
     *
     * @param string[]|MiddlewareInterface[] $middlewares
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }
        return $this;
    }

    /**
     * Add middleware in first
     *
     * @param string|MiddlewareInterface $middleware
     */
    public function prependMiddleware($middleware): self
    {
        array_unshift($this->middleware, $middleware);
        return $this;
    }

    public function lazyPipe(string $routePrefix, ContainerInterface $c, ?string $middleware = null): self
    {
        $middleware         = $middleware ?
            new RoutePrefixMiddleware($c, $routePrefix, $middleware) :
            $routePrefix;
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Undocumented function
     */
    public function shiftMiddleware(ContainerInterface $c): ?MiddlewareInterface
    {
        $middleware = array_shift($this->middleware);
        if ($middleware === null) {
            return null;
        }

        if (is_string($middleware)) {
            $middleware = $c->get($middleware);
        }
        return $middleware;
    }

    /**
     * get middleware stack
     *
     * @return iterable
     */
    public function getMiddlewareStack(): iterable
    {
        return $this->middleware;
    }
}
