<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

/**
 * Interface iFilesystem
 * @package FluencePrototype\Filesystem
 */
interface iFilesystem
{

    /**
     * @param string $directory
     * @return iFilesystem
     */
    public function cd(string $directory): iFilesystem;

    /**
     * @return iFile[]
     */
    public function listFiles(): array;

    /**
     * @return iFile[]
     */
    public function listFilesRecursively(): array;

    /**
     * @param string $filename
     * @param string $extension
     * @param string $content
     * @return iFile
     */
    public function touchFile(string $filename, string $extension, string $content = ''): iFile;

    /**
     * @param string $filename
     * @param string $extension
     * @return bool
     */
    public function fileExists(string $filename, string $extension): bool;

    /**
     * @param string $filename
     * @param string $extension
     * @return iFile|null
     */
    public function openFile(string $filename, string $extension): null|iFile;

}