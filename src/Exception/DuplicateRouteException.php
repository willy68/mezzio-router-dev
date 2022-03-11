<?php

/**
 * @see       https://github.com/mezzio/mezzio-router for the canonical source repository
 */

declare(strict_types=1);

namespace Mezzio\Router\Exception;

use DomainException;

class DuplicateRouteException extends DomainException implements
    ExceptionInterface
{
}
