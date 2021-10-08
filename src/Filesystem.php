<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use Composer\Autoload\ClassLoader;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * Class Filesystem
 * @package FluencePrototype\Filesystem
 */
class Filesystem implements iFilesystem
{

    private string $directoryPath;

    /**
     * Filesystem constructor.
     * @param string|null $directoryPath
     * @throws DirectoryNotFoundException
     * @throws InvalidDirectoryPathException
     */
    public function __construct(string $directoryPath = null)
    {
        if (!$directoryPath) {
            $reflectionClass = new ReflectionClass(objectOrClass: ClassLoader::class);
            $directoryPath = dirname($reflectionClass->getFileName(), levels: 3);
        }

        $directoryPathSanitized = filter_var(value: $directoryPath, filter: FILTER_SANITIZE_STRING);
        $directoryPathSlashesReplaced = str_replace(search: ['\\', '/'], replace: DIRECTORY_SEPARATOR, subject: $directoryPathSanitized);
        $pathInfo = pathinfo(path: $directoryPathSlashesReplaced, flags: PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo(path: $directoryPathSlashesReplaced, flags: PATHINFO_BASENAME);

        if ($directoryPathSlashesReplaced !== $pathInfo || substr(string: $pathInfo, offset: -1) === DIRECTORY_SEPARATOR) {
            throw new InvalidDirectoryPathException();
        }

        if (!is_dir(filename: $directoryPathSlashesReplaced)) {
            throw new DirectoryNotFoundException();
        }

        $this->directoryPath = $directoryPathSlashesReplaced;
    }

    /**
     * @return string
     */
    public function getDirectoryPath(): string
    {
        return $this->directoryPath;
    }

    /**
     * @inheritDoc
     * @throws InvalidDirectoryPathException|DirectoryNotFoundException
     */
    public function cd(string $directory): iFilesystem
    {
        return new Filesystem(directoryPath: $this->directoryPath . DIRECTORY_SEPARATOR . $directory);
    }

    /**
     * @inheritDoc
     * @throws InvalidFilepathException
     */
    public function listFiles(): array
    {
        $files = [];

        if ($handle = opendir(directory: $this->directoryPath)) {
            while (false !== ($filename = readdir(dir_handle: $handle))) {
                if (is_file(filename: $this->directoryPath . DIRECTORY_SEPARATOR . $filename)) {
                    $files[] = File::createFromFilePath(filePath: $this->directoryPath . DIRECTORY_SEPARATOR . $filename);
                }
            }

            closedir(dir_handle: $handle);
        }

        return $files;
    }

    /**
     * @inheritDoc
     * @throws InvalidFilepathException
     */
    public function listFilesRecursively(): array
    {
        $files = [];
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator(directory: $this->directoryPath, flags: FilesystemIterator::SKIP_DOTS);
        $recursiveIteratorIterator = new RecursiveIteratorIterator(iterator: $recursiveDirectoryIterator);

        /** @var SplFileInfo $file */
        foreach ($recursiveIteratorIterator as $file) {
            if (is_file(filename: $file->getPathname())) {
                $files[] = File::createFromFilePath(filePath: $file->getPathname());
            }
        }

        return $files;
    }

    /**
     * @inheritDoc
     * @throws InvalidFilepathException
     */
    public function touchFile(string $filename, string $extension, string $content = ''): iFile
    {
        $file = new File(directoryPath: $this->directoryPath, filename: $filename, extension: $extension);
        $file->write(content: $content);

        return $file;
    }

    /**
     * @inheritDoc
     */
    public function fileExists(string $filename, string $extension): bool
    {
        return is_file(filename: $this->directoryPath . DIRECTORY_SEPARATOR . $filename . '.' . $extension);
    }

    /**
     * @inheritDoc
     * @throws InvalidFilepathException
     */
    public function openFile(string $filename, string $extension): null|iFile
    {
        $file = new File(directoryPath: $this->directoryPath, filename: $filename, extension: $extension);

        if (!is_file(filename: $file->toFullPath())) {
            return null;
        }

        return $file;
    }

    /**
     *
     */
    public function __destruct()
    {
        clearstatcache();
    }

}