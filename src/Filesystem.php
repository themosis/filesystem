<?php

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

interface Filesystem {
	public function exists( string $path ): bool;

	public function require( string $path, array $data = [] ): mixed;

	public function require_once( string $path, array $data = [] ): mixed;

	public function is_file( string $path ): bool;

	public function read( string $path ): string;

	public function write( string $path, string $content ): void;
}
