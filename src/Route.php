<?php

/**
 * @see       https://github.com/mezzio/mezzio-router for the canonical source repository
 */

declare(strict_types=1);

namespace Mezzio\Router;

use Mezzio\Router\Middleware\Stack\MiddlewareAwareStackTrait;
use Psr\Http\Message\ServerRequestInterface;

use function array_map;
use function array_reduce;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function preg_match;
use function strcmp;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;

/**
 * Value object representing a single route.
 *
 * Routes are a combination of path, middleware, and HTTP methods; two routes
 * representing the same path and overlapping HTTP methods are not allowed,
 * while two routes representing the same path and non-overlapping HTTP methods
 * can be used (and should typically resolve to different middleware).
 *
 * Internally, only those three properties are required. However, underlying
 * router implementations may allow or require additional information, such as
 * information defining how to generate a URL from the given route, qualifiers
 * for how segments of a route match, or even default values to use. These may
 * be provided after instantiation via the "options" property and related
 * setOptions() method.
 */
class Route implements RouteInterface
{
    use MiddlewareAwareStackTrait;

    public const HTTP_METHOD_ANY       = null;
    public const HTTP_METHOD_SEPARATOR = ':';
    public const HTTP_SCHEME_ANY       = null;

    /** @var null|string[] HTTP methods allowed with this route. */
    private $methods;

    /** @var string|callable associated with route. */
    private $callback;

    /** @var array Options related to this route to pass to the routing implementation. */
    private $options = [];

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    /** @var ?string */
    protected $host;

    /** @var ?int */
    protected $port;

    /** @var null|string[] */
    protected $schemes;

    /**
     * parent group
     *
     * @var RouteGroup
     */
    private $group;

    /**
     * @param string              $path Path to match.
     * @param string|callable            $callback to use when this route is matched.
     * @param null|string[]       $methods Allowed HTTP methods; defaults to HTTP_METHOD_ANY.
     * @param null|string         $name the route name
     */
    public function __construct(
        string $path,
        $callback,
        ?string $name = null,
        ?array $methods = self::HTTP_METHOD_ANY
    ) {
        $this->path     = $path;
        $this->callback = $callback;
        $this->methods  = is_array($methods) ? $this->validateHttpMethods($methods) : $methods;

        if (! $name) {
            $name = $this->methods === self::HTTP_METHOD_ANY
                ? $path
                : $path . '^' . implode(self::HTTP_METHOD_SEPARATOR, $this->methods);
        }
        $this->name = $name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the route name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * get Route callback (controller method)
     *
     * @return string|callable|mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return null|string[] Returns HTTP_METHOD_ANY or array of allowed methods.
     */
    public function getAllowedMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * Indicate whether the specified method is allowed by the route.
     *
     * @param string $method HTTP method to test.
     */
    public function allowsMethod(string $method): bool
    {
        $method = strtoupper($method);
        return $this->allowsAnyMethod() || in_array($method, $this->methods, true);
    }

    /**
     * Indicate whether any method is allowed by the route.
     */
    public function allowsAnyMethod(): bool
    {
        return $this->methods === self::HTTP_METHOD_ANY;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * get schemes array available for this route
     *
     * @return null|string[] Returns HTTP_SCHEME_ANY or array of allowed schemes.
     */
    public function getSchemes(): ?array
    {
        return $this->schemes;
    }

    /**
     * Indicate whether the specified scheme is allowed by the route.
     *
     * @param string $method HTTP method to test.
     */
    public function allowsScheme(string $scheme): bool
    {
        $scheme = strtolower($scheme);
        return $this->allowsAnyScheme() || in_array($scheme, $this->schemes, true);
    }

    /**
     * Indicate whether any scheme is allowed by the route.
     */
    public function allowsAnyScheme(): bool
    {
        return $this->schemes === self::HTTP_SCHEME_ANY;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * set schemes available for this route
     *
     * @param string[] $schemes
     * @return Route
     */
    public function setSchemes(array $schemes): self
    {
        $this->schemes = $schemes;
        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get the parent group
     */
    public function getParentGroup(): ?RouteGroup
    {
        return $this->group;
    }

    /**
     * Set the parent group
     *
     * @return Route
     */
    public function setParentGroup(RouteGroup $group): self
    {
        $this->group = $group;
        $prefix      = $this->group->getPrefix();
        $path        = $this->getPath();

        if (strcmp($prefix, substr($path, 0, strlen($prefix))) !== 0) {
            $path       = $prefix . $path;
            $this->path = $path;
        }

        return $this;
    }

    /**
     * Validate the provided HTTP method names.
     *
     * Validates, and then normalizes to upper case.
     *
     * @param string[] $methods An array of HTTP method names.
     * @return string[]
     * @throws Exception\InvalidArgumentException For any invalid method names.
     */
    private function validateHttpMethods(array $methods): array
    {
        if (empty($methods)) {
            throw new Exception\InvalidArgumentException(
                'HTTP methods argument was empty; must contain at least one method'
            );
        }

        if (
            false === array_reduce($methods, function ($valid, $method) {
                if (false === $valid) {
                    return false;
                }

                if (! is_string($method)) {
                    return false;
                }

                if (! preg_match('/^[!#$%&\'*+.^_`\|~0-9a-z-]+$/i', $method)) {
                    return false;
                }

                return $valid;
            }, true)
        ) {
            throw new Exception\InvalidArgumentException('One or more HTTP methods were invalid');
        }

        return array_map('strtoupper', $methods);
    }

    protected function isExtraConditionMatch(Route $route, ServerRequestInterface $request): bool
    {
        // check for scheme condition
        if (! $route->allowsScheme($request->getUri()->getScheme())) {
            return false;
        }

        // check for domain condition
        $host = $route->getHost();
        if ($host !== null && $host !== $request->getUri()->getHost()) {
            return false;
        }

        // check for port condition
        $port = $route->getPort();
        return ! ($port !== null && $port !== $request->getUri()->getPort());
    }
}
