<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
// SPDX-FileCopyrightText: 2025 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

use Closure;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Themosis\Components\Filesystem\Exceptions\FilesystemException;

final class LocalFilesystem implements Filesystem
{
    public function exists(string $path): bool
    {
        return self::sandbox(function () use ($path): bool {
            return file_exists($path);
        })(fn(bool $exists) => $exists);
    }

    public function doesNotExist(string $path): bool
    {
        return ! $this->exists($path);
    }

    public function require(string $path, array $data = []): mixed
    {
        return self::sandbox(function () use ($path, $data): mixed {
            $__path = $path;
            $__data = $data;

            return (static function () use ($__path, $__data) {
                // phpcs:ignore
                extract($__data, EXTR_SKIP);

                return require $__path;
            })();
        })(fn(mixed $content) => $content);
    }

    public function requireOnce(string $path, array $data = []): mixed
    {
        return self::sandbox(function () use ($path, $data): mixed {
            $__path = $path;
            $__data = $data;

            return (static function () use ($__path, $__data) {
                // phpcs:ignore
                extract($__data, EXTR_SKIP);

                return require_once $__path;
            })();
        })(fn(mixed $content) => $content);
    }

    public function isFile(string $path): bool
    {
        return self::sandbox(function () use ($path): bool {
            return is_file($path);
        })(fn(bool $isFile) => $isFile);
    }

    public function read(string $path): string
    {
        return self::sandbox(function () use ($path): string {
            return file_get_contents($path);
        })(fn(string $content) => $content);
    }

    public function write(string $path, string $content): void
    {
        self::sandbox(function () use ($path, $content): void {
            file_put_contents($path, $content);
        });
    }

    public function isDirectory(string $path): bool
    {
        return self::sandbox(function () use ($path): bool {
            return is_dir($path);
        })(fn(bool $isDir) => $isDir);
    }

    public function makeDirectory(string $path, ?Permissions $permissions = null): void
    {
        self::sandbox(function () use ($path, $permissions): void {
            $permissions = $permissions ?? PosixPermissions::default();

            mkdir($path, octdec((string) $permissions), true);
        });
    }

    public function deleteFile(string $path): void
    {
        self::sandbox(function () use ($path): void {
            unlink($path);
        });
    }

    public function deleteDirectory(string $path, bool $recursive = false): void
    {
        self::sandbox(function () use ($path, $recursive): void {
            if (! $recursive) {
                rmdir($path);
            } else {
                $iterator = new RecursiveIteratorIterator(
                    iterator: new RecursiveDirectoryIterator(
                        directory: $path,
                        flags: FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO,
                    ),
                    mode: RecursiveIteratorIterator::CHILD_FIRST
                );

                /**
                 * The loop is responsible to delete all children
                 * elements except the root directory.
                 */
                foreach ($iterator as $element) {
                    /** @var SplFileInfo $element */
                    $elementPath = $element->getPathname();

                    if (is_link($elementPath)) {
                        $this->deleteFile($element->getPath());
                    } elseif ($this->isDirectory($elementPath)) {
                        $this->deleteDirectory($elementPath, false);
                    } else {
                        $this->deleteFile($elementPath);
                    }
                }

                /**
                 * This last statement finally deletes the root
                 * directory, now that it is empty.
                 */
                $this->deleteDirectory($path, false);
            }
        });
    }

    private static function sandbox(Closure $callback): Closure
    {
        /**
         * Temporarily register a custom error handler in order
         * to transform all emitted PHP errors as exceptions.
         */
        set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline) {
            throw new FilesystemException($errstr, 0, $errno, $errfile, $errline);
        });

        try {
            $result = $callback();

            /**
             * Return a Closure so if the sandboxed function is
             * expected to return a value, we can declare the type
             * of the returned value per implementation.
             */
            return static function (Closure $eval) use ($result) {
                return $eval($result);
            };
        } finally {
            /**
             * Always restore the error handler immediately.
             */
            restore_error_handler();
        }
    }
}
