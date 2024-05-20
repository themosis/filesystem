<?php

declare(strict_types=1);

namespace Themosis\Components\Filesystem\Tests;

use PHPUnit\Framework\Attributes\Test;
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
}
