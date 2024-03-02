<?php

declare(strict_types=1);

namespace BoxDesigner\PreBuilt;

use BoxDesigner\LineAsciiCharsInterface;

class SingleLineBorder implements LineAsciiCharsInterface
{
    public function topLeft(): string
    {
        return '┌';
    }

    public function topRight(): string
    {
        return '┐';
    }

    public function bottomLeft(): string
    {
        return '└';
    }

    public function bottomRight(): string
    {
        return '┘';
    }

    public function horizontalLine(): string
    {
        return '─';
    }

    public function verticalLine(): string
    {
        return '│';
    }
}
