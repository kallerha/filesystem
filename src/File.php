<?php

declare(strict_types=1);

namespace FluencePrototype\Filesystem;

use JetBrains\PhpStorm\Pure;
use SplFileObject;

/**
 * Class File
 * @package FluencePrototype\Filesystem
 */
class File implements iFile
{

    private string $directoryPath;
    private string $filename;
    private string $extension;
    private mixed $handle = null;
    private SplFileObject $fileObject;

    /**
     * File constructor.
     * @param string $directoryPath
     * @param string $filename
     * @param string $extension
     * @throws InvalidFilePathException
     */
    public function __construct(string $directoryPath, string $filename, string $extension)
    {
        $directoryPathSanitized = filter_var(value: $directoryPath);
        $filenameSanitized = filter_var(value: $filename);
        $extensionSanitized = filter_var(value: $extension);

        if (!($directoryPathSanitized !== '' && $extensionSanitized !== '')) {
            throw new InvalidFilePathException();
        }

        $this->directoryPath = $directoryPathSanitized;
        $this->filename = $filenameSanitized;
        $this->extension = $extensionSanitized;

        if (is_file(filename: $this->toFullPath())) {
            $this->fileObject = new SplFileObject(filename: $this->toFullPath(), mode: 'r');
        }
    }

    /**
     * @inheritDoc
     */
    public function getDirectoryPath(): string
    {
        return $this->directoryPath;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function getAsCompilableContent(array $data = []): string
    {
        extract($data);
        ob_start();

        include $this->toFullPath();
        echo PHP_EOL;

        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function getLines(): null|array
    {
        if ($this->fileObject) {
            if ($lines = file($this->toFullPath())) {
                return $lines;
            }

            return null;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function toFullPath(): string
    {
        return $this->directoryPath . '/' . $this->filename . '.' . $this->extension;
    }

    /**
     * @return mixed
     */
    private function getHandle(): mixed
    {
        if (!$this->handle) {
            $this->handle = fopen(filename: $this->toFullPath(), mode: 'a');
        }

        return $this->handle;
    }

    /**
     *
     */
    private function closeHandle(): void
    {
        if ($this->handle) {
            fclose(stream: $this->handle);
        }
    }

    /**
     * @inheritDoc
     */
    public function write(string $content): iFile
    {
        $handle = $this->getHandle();

        fwrite(stream: $handle, data: $content);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function writeLine(string $content): iFile
    {
        $handle = $this->getHandle();

        fwrite(stream: $handle, data: $content . PHP_EOL);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear(): iFile
    {
        $handle = $this->getHandle();

        ftruncate(stream: $handle, size: 0);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        unlink(filename: $this->toFullPath());
    }

    /**
     * @inheritDoc
     * @throws InvalidFilePathException
     */
    public static function createFromFilePath(string $filePath): iFile
    {
        $directoryPath = pathinfo(path: $filePath, flags: PATHINFO_DIRNAME);
        $filename = pathinfo(path: $filePath, flags: PATHINFO_FILENAME);
        $extension = pathinfo(path: $filePath, flags: PATHINFO_EXTENSION);

        return new File(directoryPath: $directoryPath, filename: $filename, extension: $extension);
    }

    /**
     * @inheritDoc
     */
    public function current(): string|array|bool
    {
        return $this->fileObject->current();
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->fileObject->next();
    }

    /**
     * @inheritDoc
     */
    public function key(): float|bool|int|string|null
    {
        return $this->fileObject->key();
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->fileObject->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->fileObject->rewind();
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function __toString(): string
    {
        return $this->toFullPath();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->closeHandle();

        clearstatcache();
    }

}