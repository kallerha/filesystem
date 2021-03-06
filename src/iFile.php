<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use Iterator;
use Stringable;

/**
 * Interface iFile
 * @package FluencePrototype\Filesystem
 */
interface iFile extends Iterator, Stringable
{

    /**
     * @return string
     */
    public function getDirectoryPath(): string;

    /**
     * @return string
     */
    public function getFilename(): string;

    /**
     * @return string
     */
    public function getExtension(): string;

    /**
     * @param array $data
     * @return string
     */
    public function getAsCompilableContent(array $data = []): string;

    /**
     * @return array|null
     */
    public function getLines(): null|array;

    /**
     * @return string
     */
    public function toFullPath(): string;

    /**
     * @param string $content
     */
    public function write(string $content): iFile;

    /**
     * @param string $content
     */
    public function writeLine(string $content): iFile;

    /**
     * @return iFile
     */
    public function clear(): iFile;

    /**
     *
     */
    public function delete(): void;

    /**
     * @param string $filePath
     * @return iFile
     */
    public static function createFromFilePath(string $filePath): iFile;

}