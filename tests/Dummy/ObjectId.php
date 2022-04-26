<?php

namespace Bvtterfly\LaravelHashids\Tests\Dummy;

use Stringable;

class ObjectId implements Stringable
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->id;
    }
}
