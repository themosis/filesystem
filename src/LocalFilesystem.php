<?php

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

use Themosis\Components\Filesystem\Exceptions\FileDoesNotExist;
use Themosis\Components\Filesystem\Exceptions\InvalidFileException;
use Themosis\Components\Filesystem\Exceptions\ReadFileException;
use Themosis\Components\Filesystem\Exceptions\WriteFileException;

final class LocalFilesystem implements Filesystem {
	public function exists( string $path ): bool {
		return file_exists( $path );
	}

	public function require( string $path, array $data = [] ): mixed {
		if ( ! $this->is_file( $path ) ) {
			throw new InvalidFileException( sprintf( 'Invalid file given at path %s', $path ) );
		}

		$__path = $path;
		$__data = $data;

		return ( static function () use ( $__path, $__data ) {
            // phpcs:ignore
			extract( $__data, EXTR_SKIP );

			return require $__path;
		} )();
	}

	public function require_once( string $path, array $data = [] ): mixed {
		if ( ! $this->is_file( $path ) ) {
			throw new InvalidFileException( sprintf( 'Invalid file given at path %s', $path ) );
		}

		$__path = $path;
		$__data = $data;

		return ( static function () use ( $__path, $__data ) {
            // phpcs:ignore
			extract( $__data, EXTR_SKIP );

			return require_once $__path;
		} )();
	}

	public function is_file( string $path ): bool {
		return is_file( $path );
	}

	public function read( string $path ): string {
		if ( ! $this->exists( $path ) ) {
			throw new FileDoesNotExist( sprintf( 'File not found at path %s', $path ) );
		}

		$content = file_get_contents( $path );

		if ( false === $content ) {
			throw new ReadFileException( sprintf( 'Cannot read file content for file at path %s', $path ) );
		}

		return $content;
	}

	public function write( string $path, string $content ): void {
		$bytes = file_put_contents( $path, $content );

		if ( false === $bytes ) {
			throw new WriteFileException( sprintf( 'Could not write file content at path %s', $path ) );
		}
	}
}
