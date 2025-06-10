<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
// SPDX-FileCopyrightText: 2025 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

interface Filesystem
{
    public function exists(string $path): bool;

    public function doesNotExist(string $path): bool;

    public function require(string $path, array $data = []): mixed;

    public function requireOnce(string $path, array $data = []): mixed;

    public function isLink(string $path): bool;

    public function isFile(string $path): bool;

    public function read(string $path): string;

    public function write(string $path, string $content): void;

    public function hardlink(string $original, string $target): void;

    public function symlink(string $original, string $target): void;

    public function isDirectory(string $path): bool;

    public function makeDirectory(string $path, ?Permissions $permissions = null): void;

    public function deleteLink(string $path): void;

    public function deleteFile(string $path): void;

    public function deleteDirectory(string $path, bool $recursive = false): void;
}
