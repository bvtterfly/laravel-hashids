<?php

namespace Bvtterfly\LaravelHashids\Exceptions;

use Exception;

class InvalidOption extends Exception
{
    public static function missingHashIdField(): static
    {
        return new static('Could not determine in which field the hash id should be saved');
    }

    public static function invalidMinLength(): static
    {
        return new static('Minimum length should be greater than or equal zero');
    }

    public static function invalidHashIdType(): static
    {
        return new static('Hash id type should be "int" or "hex"');
    }
}
