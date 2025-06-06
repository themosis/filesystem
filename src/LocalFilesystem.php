<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
// SPDX-FileCopyrightText: 2025 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

use Themosis\Components\Filesystem\Exceptions\CannotDeleteFile;
use Themosis\Components\Filesystem\Exceptions\CannotMakeDirectory;
use Themosis\Components\Filesystem\Exceptions\FileDoesNotExist;
use Themosis\Components\Filesystem\Exceptions\InvalidFile;
use Themosis\Components\Filesystem\Exceptions\CannotReadFromFile;
use Themosis\Components\Filesystem\Exceptions\CannotWriteToFile;
use Themosis\Components\Filesystem\Exceptions\DirectoryAlreadyExists;

final class LocalFilesystem implements Filesystem
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function doesNotExist(string $path): bool
    {
        return ! $this->exists($path);
    }

    public function require(string $path, array $data = []): mixed
    {
        if (! $this->isFile($path)) {
            throw new InvalidFile(sprintf('Invalid file given at path %s', $path));
        }

        $__path = $path;
        $__data = $data;

        return (static function () use ($__path, $__data) {
            // phpcs:ignore
            extract($__data, EXTR_SKIP);

            return require $__path;
        })();
    }

    public function requireOnce(string $path, array $data = []): mixed
    {
        if (! $this->isFile($path)) {
            throw new InvalidFile(sprintf('Invalid file given at path %s', $path));
        }

        $__path = $path;
        $__data = $data;

        return (static function () use ($__path, $__data) {
            // phpcs:ignore
            extract($__data, EXTR_SKIP);

            return require_once $__path;
        })();
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function read(string $path): string
    {
        if ($this->doesNotExist($path)) {
            throw new FileDoesNotExist(sprintf('File not found at path %s', $path));
        }

        $content = file_get_contents($path);

        if (false === $content) {
            throw new CannotReadFromFile(sprintf('Cannot read content for file at path %s', $path));
        }

        return $content;
    }

    public function write(string $path, string $content): void
    {
        $bytes = file_put_contents($path, $content);

        if (false === $bytes) {
            throw new CannotWriteToFile(sprintf('Cannot write file content at path %s', $path));
        }
    }

    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public function makeDirectory(string $path, ?Permissions $permissions = null): void
    {
        if ($this->isDirectory($path)) {
            throw new DirectoryAlreadyExists(sprintf('Directory exists at path %s', $path));
        }

        $permissions = $permissions ?? PosixPermissions::default();
        $result = mkdir($path, octdec((string) $permissions), true);

        if (false === $result) {
            throw new CannotMakeDirectory(sprintf('Cannot make directory at path %s', $path));
        }
    }

    public function deleteFile(string $path): void
    {
        if ($this->doesNotExist($path)) {
            throw new FileDoesNotExist(sprintf('File not found at path %s', $path));
        }

        if (! $this->isFile($path)) {
            throw new InvalidFile(sprintf('Invalid file given at path %s', $path));
        }
        
        $result = unlink($path);
        
        // @codeCoverageIgnoreStart
        if (false === $result) {
            throw new CannotDeleteFile(sprintf('Cannot delete file at path %s', $path));
        }
        // @codeCoverageIgnoreEnd
    }
}
