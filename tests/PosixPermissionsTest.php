<?php

// SPDX-FileCopyrightText: 2025 Julien Lambé <julien@themosis.com>
//
// SPDX-License-Identifier: GPL-3.0-or-later

declare(strict_types=1);

namespace Themosis\Components\Filesystem\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Themosis\Components\Filesystem\PosixPermissions;

final class PosixPermissionsTest extends TestCase
{
    #[Test]
    public function it_can_not_allow_out_of_range_bits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Owner bits value of 0 is out of the [1-7] range.');

        new PosixPermissions(
            ownerBits: 0,
            groupBits: -1,
            othersBits: 8,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Group bits value of -1 is out of the [1-7] range.');

        new PosixPermissions(
            ownerBits: 1,
            groupBits: -1,
            othersBits: 1
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Others bits value of 12 is out of the [1-7] range.');

        new PosixPermissions(
            ownerBits: 7,
            groupBits: 5,
            othersBits: 12
        );
    }

    #[Test]
    public function it_can_provide_a_default_permission_octal_string(): void
    {
        $permissions = PosixPermissions::default();

        $this->assertSame('0777', (string) $permissions);
        $this->assertSame(0777, octdec((string) $permissions));
    }
}
