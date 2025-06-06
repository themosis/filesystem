<?php

// SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem\Tests;

use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Filesystem\Exceptions\DirectoryAlreadyExists;
use Themosis\Components\Filesystem\Exceptions\FileDoesNotExist;
use Themosis\Components\Filesystem\Exceptions\InvalidFile;
use Themosis\Components\Filesystem\Filesystem;
use Themosis\Components\Filesystem\LocalFilesystem;

final class LocalFilesystemTest extends TestCase
{   
    #[Test]
    public function it_can_ensure_local_filesystem_implementation_as_filesytem_interface(): void
    {
        $filesystem = new LocalFilesystem();

        $this->assertInstanceOf(Filesystem::class, $filesystem);
    }

    #[Test]
    public function it_can_check_if_file_exists_or_not(): void
    {
        $filesystem = new LocalFilesystem();

        $this->assertTrue($filesystem->exists(__DIR__ . '/fixtures/file-a.php'));
        $this->assertTrue($filesystem->exists(__DIR__ . '/fixtures/info.md'));
        $this->assertTrue($filesystem->exists(__DIR__ . '/fixtures/config'));

        $this->assertFalse($filesystem->exists(__DIR__ . '/path/does/not/exist'));
    }

    #[Test]
    public function it_can_check_if_file_is_a_file(): void
    {
        $filesystem = new LocalFilesystem();

        $this->assertTrue($filesystem->isFile(__DIR__ . '/fixtures/file-a.php'));
        $this->assertTrue($filesystem->isFile(__DIR__ . '/fixtures/config'));

        $this->assertFalse($filesystem->isFile(__DIR__ . '/fixtures'));
    }

    #[Test]
    public function it_can_not_require_file_if_given_path_is_a_directory(): void
    {
        $filesystem = new LocalFilesystem();

        $this->expectException(InvalidFile::class);

        $filesystem->require(__DIR__ . '/fixtures');
    }

    #[Test]
    public function it_can_not_require_once_file_if_given_path_is_a_directory(): void
    {
        $filesystem = new LocalFilesystem();

        $this->expectException(InvalidFile::class);

        $filesystem->requireOnce(__DIR__ . '/fixtures');
    }

    #[Test]
    public function it_can_require_file_and_return_its_content(): void
    {
        $filesystem = new LocalFilesystem();

        $result = $filesystem->require(__DIR__ . '/fixtures/file-b.php');

        $this->assertNotEmpty($result);
        $this->assertTrue('bar' === $result['foo']);
    }

    #[Test]
    public function it_can_require_file_and_return_no_content(): void
    {
        $filesystem = new LocalFilesystem();

        // If there is not a return statement in the included file
        // and if there are no errors, it returns the integer 1.
        $result = $filesystem->require(__DIR__ . '/fixtures/file-a.php');

        $this->assertSame(1, $result);
    }

    #[Test]
    public function it_can_require_file_and_share_data_with_required_file(): void
    {
        $filesystem = new LocalFilesystem();

        $result = $filesystem->require(
            path: __DIR__ . '/fixtures/file-with-variables.php',
            data: [
                'foo' => 'hello',
                'bar' => 'world',
                'baz' => 42,
            ],
        );

        $this->assertSame('hello', $result['foo']);
        $this->assertSame('world', $result['bar']);
        $this->assertSame(42, $result['baz']);
    }

    #[Test]
    public function it_can_require_once_a_file(): void
    {
        $filesystem = new LocalFilesystem();

        $result = $filesystem->requireOnce(
            path: __DIR__ . '/fixtures/file-with-variables.php',
            data: [
                'foo' => 'hello',
                'bar' => 'world',
                'baz' => 42,
            ],
        );

        $this->assertSame('hello', $result['foo']);
        $this->assertSame('world', $result['bar']);
        $this->assertSame(42, $result['baz']);

        $result = $filesystem->requireOnce(
            path: __DIR__ . '/fixtures/file-with-variables.php',
            data: [
                'foo' => 'hello',
                'bar' => 'world',
                'baz' => 42,
            ],
        );

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_not_read_file_if_it_does_not_exist(): void
    {
        $filesystem = new LocalFilesystem();

        $this->expectException(FileDoesNotExist::class);

        $filesystem->read(__DIR__ . '/path/does/not/exist');
    }

    #[Test]
    public function it_can_read_a_file(): void
    {
        $filesystem = new LocalFilesystem();

        $content = $filesystem->read(__DIR__ . '/fixtures/info.md');

        $expected = <<<RESULT
Information
===========

This is a test file for the Themosis Filesystem component.\n
RESULT;

        $this->assertSame($expected, $content);
    }

    #[Test]
    public function it_can_write_a_file(): void
    {
        $filesystem = new LocalFilesystem();

        $file = __DIR__ . '/fixtures/write.txt';

        $filesystem->write($file, 'Hello World!');

        $expected = 'Hello World!';

        $this->assertSame($expected, $filesystem->read($file));
    }

    #[Test]
    public function it_can_check_if_path_is_a_directory(): void
    {
        $filesystem = new LocalFilesystem();

        $this->assertTrue($filesystem->isDirectory(__DIR__));
        $this->assertTrue($filesystem->isDirectory(__DIR__ . '/fixtures'));
        $this->assertFalse($filesystem->isDirectory('path/does/not/exist'));
        $this->assertFalse($filesystem->isDirectory(__DIR__ . '/fixtures/file-a.php'));
    }

    #[Test]
    public function itCanMakeADirectory_ifDirectoryDoesNotExist(): void
    {
        $filesystem = new LocalFilesystem();

        $path = __DIR__ . '/fixtures/new-dir';

        $this->assertFalse($filesystem->isDirectory($path));

        $filesystem->makeDirectory($path);

        $this->assertTrue($filesystem->isDirectory($path));

        rmdir($path);
    }

    #[Test]
    public function itCanNotMakeADirectory_ifDirectoryAlreadyExists(): void
    {
        $filesystem = new LocalFilesystem();

        $path = __DIR__ . '/fixtures';

        $this->assertTrue($filesystem->isDirectory($path));

        $this->expectException(DirectoryAlreadyExists::class);
        
        $filesystem->makeDirectory($path);
    }

    #[Test]
    public function itCanMakeNestedDirectory(): void
    {
        $filesystem = new LocalFilesystem();

        $rootpath = __DIR__ . '/fixtures/a-dir';
        $parentpath = $rootpath . '/with-parent';
        $path = $parentpath . '/and-child';

        $this->assertFalse($filesystem->isDirectory($path));

        $filesystem->makeDirectory($path);

        $this->assertTrue($filesystem->isDirectory($rootpath));
        $this->assertTrue($filesystem->isDirectory($parentpath));
        $this->assertTrue($filesystem->isDirectory($path));

        rmdir($path);
    }
}
