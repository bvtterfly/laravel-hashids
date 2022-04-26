<?php

namespace Bvtterfly\LaravelHashids;

use Bvtterfly\LaravelHashids\Exceptions\InvalidOption;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

trait HasHashId
{
    protected HashIdOptions $hashIdOptions;

    abstract public function getHashIdOptions(): HashIdOptions;

    protected static function bootHasHashId(): void
    {
        static::created(function (Model $model) {
            $model->generateHashIdOnCreatedEvent();
            $model->save();
        });
        static::creating(function (Model $model) {
            $model->generateHashIdOnCreatingEvent();
        });
    }

    protected function generateHashIdOnCreatingEvent(): void
    {
        $this->hashIdOptions = $this->getHashIdOptions();

        if ($this->hashIdOptions->autoGeneratedField) {
            return;
        }

        $this->addHashId();
    }

    protected function generateHashIdOnCreatedEvent(): void
    {
        $this->hashIdOptions = $this->getHashIdOptions();

        if (! $this->hashIdOptions->autoGeneratedField) {
            return;
        }

        $this->addHashId();
    }

    protected function addHashId(): void
    {
        $this->ensureValidHashIdOptions();

        $hashId = $this->generateHashId();

        $hashIdField = $this->hashIdOptions->hashIdField;

        $this->$hashIdField = $hashId;
    }

    protected function generateHashId(): string
    {
        $value = $this->getHashIdFromValue();

        if ($this->hashIdOptions->type == 'int') {
            return $this->getGenerator()->encode($value);
        }

        return $this->getGenerator()->encodeHex($value);
    }

    protected function getHashIdFromValue()
    {
        return $this->{$this->getHashIdFromField()};
    }

    protected function getHashIdFromField(): string
    {
        return $this->hashIdOptions->generateHashIdFrom ?? $this->getKeyName();
    }

    protected function getGenerator(): Hashids
    {
        return new Hashids(
            $this->getSalt(),
            $this->hashIdOptions->minHashLength,
            $this->hashIdOptions->alphabet
        );
    }

    protected function getSalt()
    {
        $salt = config('hashids.salt');

        if ($this->hashIdOptions->generateUniqueHashIds) {
            $class = get_class($this);

            return "{$salt}:{$class}";
        }

        return $salt;
    }

    protected function ensureValidHashIdOptions()
    {
        if (! strlen($this->hashIdOptions->hashIdField)) {
            throw InvalidOption::missingHashIdField();
        }

        if ($this->hashIdOptions->minHashLength < 0) {
            throw InvalidOption::invalidMinLength();
        }
    }
}
