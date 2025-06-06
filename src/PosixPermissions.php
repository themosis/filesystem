<?php

declare(strict_types=1);

namespace Themosis\Components\Filesystem;

use InvalidArgumentException;

final class PosixPermissions implements Permissions
{
    public function __construct(
        private int $ownerBits,
        private int $groupBits,
        private int $othersBits,
    ) {
        foreach (['owner' => $ownerBits, 'group' => $groupBits, 'others' => $othersBits] as $name => $value) {
            if ($this->isOutOfRange($value)) {
                throw new InvalidArgumentException(sprintf(
                    '%s bits value of %d is out of the [1-7] range.',
                    ucfirst($name),
                    $value,
                ));
            }
        }
    }

    private function isOutOfRange(int $bits): bool
    {
        return (1 > $bits || 7 < $bits);
    }

    public static function default(): self
    {
        return new self(
            ownerBits: 7,
            groupBits: 7,
            othersBits: 7,
        );
    }

    public function __toString(): string
    {
        return sprintf('0%d%d%d', $this->ownerBits, $this->groupBits, $this->othersBits);
    }
}
