<?php

namespace Bvtterfly\LaravelHashids;

use Hashids\Hashids;

class HashIdBuilder
{

    public static function build(HashIdOptions $options): Hashids
    {
        return new Hashids(
            $options->salt(),
            $options->minHashLength,
            $options->alphabet
        );
    }
}
