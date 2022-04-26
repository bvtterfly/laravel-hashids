<?php

namespace Bvtterfly\LaravelHashids\Tests\Dummy\Models;

use Bvtterfly\LaravelHashids\HasHashId;
use Bvtterfly\LaravelHashids\HashIdOptions;
use Illuminate\Database\Eloquent\Model;

class InvalidHashTypeModel extends Model
{
    use HasHashId;

    private bool $saved = false;
    private bool $updated = false;

    public function getHashIdOptions(): HashIdOptions
    {
        return HashIdOptions::create()->saveHashIdTo('hashid')->setType('object');
    }

    public function save($options = [])
    {
        if (! $this->saved) {
            if ($this->fireModelEvent('creating') === false) {
                return false;
            }
            $this->saved = true;
            $this->exists = true;
            $this->id = $options['id'] ?? 1;
            $this->fireModelEvent('created', false);
        }

        if ($this->saved && $this->isDirty('hashid') && ! $this->updated) {
            $this->updated = true;
        }
    }

    public function getSaved()
    {
        return $this->saved;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getHashid()
    {
        return $this->hashid;
    }
}
