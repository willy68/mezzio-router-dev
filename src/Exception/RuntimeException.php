<?php

/**
 * @see       https://github.com/mezzio/mezzio-router for the canonical source repository
 */

declare(strict_types=1);

namespace Mezzio\Router\Exception;

use RuntimeException as PhpRuntimeException;

class RuntimeException extends PhpRuntimeException implements ExceptionInterface
{
}
