<?php

declare(strict_types=1);

namespace Themosis\Components\Filesystem\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Filesystem\Exceptions\InvalidFileException;
use Themosis\Components\Filesystem\LocalFilesystem;

final class LocalFilesystemTest extends TestCase {
	#[Test]
	public function it_can_check_if_file_exists_or_not(): void {
		$filesystem = new LocalFilesystem();

		$this->assertTrue( $filesystem->exists( __DIR__ . '/fixtures/file-a.php' ) );
		$this->assertTrue( $filesystem->exists( __DIR__ . '/fixtures/info.md' ) );
		$this->assertTrue( $filesystem->exists( __DIR__ . '/fixtures/config' ) );

		$this->assertFalse( $filesystem->exists( __DIR__ . '/path/does/not/exist' ) );
	}

	#[Test]
	public function it_can_check_if_file_is_a_file(): void {
		$filesystem = new LocalFilesystem();

		$this->assertTrue( $filesystem->is_file( __DIR__ . '/fixtures/file-a.php' ) );
		$this->assertTrue( $filesystem->is_file( __DIR__ . '/fixtures/config' ) );

		$this->assertFalse( $filesystem->is_file( __DIR__ . '/fixtures' ) );
	}

	#[Test]
	public function it_can_not_require_file_if_given_path_is_a_directory(): void {
		$filesystem = new LocalFilesystem();

		$this->expectException( InvalidFileException::class );

		$filesystem->require( __DIR__ . '/fixtures' );
	}

	#[Test]
	public function it_can_require_file_and_return_its_content(): void {
		$filesystem = new LocalFilesystem();

		$result = $filesystem->require( __DIR__ . '/fixtures/file-b.php' );

		$this->assertNotEmpty( $result );
		$this->assertTrue( 'bar' === $result['foo'] );
	}

	#[Test]
	public function it_can_require_file_and_return_no_content(): void {
		$filesystem = new LocalFilesystem();

		// If there is not a return statement in the included file
		// and if there are no errors, it returns the integer 1.
		$result = $filesystem->require( __DIR__ . '/fixtures/file-a.php' );

		$this->assertSame( 1, $result );
	}
}
