<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use Exception;
use Throwable;

/**
 * Class InvalidFilePathException
 * @package FluencePrototype\Filesystem
 */
class InvalidFilePathException extends Exception
{

    /**
     * InvalidFilePathException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }

}