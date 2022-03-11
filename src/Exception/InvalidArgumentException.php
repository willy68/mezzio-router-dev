<?php

/**
 * @see       https://github.com/mezzio/mezzio-router for the canonical source repository
 */

declare(strict_types=1);

namespace Mezzio\Router\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;

class InvalidArgumentException extends PhpInvalidArgumentException implements ExceptionInterface
{
}
