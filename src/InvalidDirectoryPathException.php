<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use Exception;
use Throwable;

/**
 * Class InvalidDirectoryPathException
 * @package FluencePrototype\Filesystem
 */
class InvalidDirectoryPathException extends Exception
{

    /**
     * InvalidDirectoryPathException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
   public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }

}