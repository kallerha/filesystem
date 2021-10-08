<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

/**
 * Class DirectoryNotFoundException
 * @package FluencePrototype\Filesystem
 */
class DirectoryNotFoundException extends Exception
{

    /**
     * DirectoryNotFoundException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }

}