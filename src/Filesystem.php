<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

interface Filesystem
{
    public function exists(string $path): bool;

    public function require(string $path, array $data = []): mixed;

    public function requireOnce(string $path, array $data = []): mixed;

    public function isFile(string $path): bool;

    public function read(string $path): string;

    public function write(string $path, string $content): void;

    public function isDirectory(string $path): bool;
}
