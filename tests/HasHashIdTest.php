<?php

use Bvtterfly\LaravelHashids\Exceptions\InvalidOption;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\AutoIncrementModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\AutoIncrementModel2;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\CustomFromKeyModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\HexTypeModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\InvalidHashTypeModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\InvalidMinimumHashLengthModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\TestModel1;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\TestModel2;
use Bvtterfly\LaravelHashids\Tests\Dummy\Models\WithoutHashIdFieldModel;
use Bvtterfly\LaravelHashids\Tests\Dummy\ObjectId;
use Hashids\Hashids;

beforeEach(function () {
    config()->set('hashids.salt', 'test');
});


it('can generate hash id for incrementing model', function () {
    $model = new AutoIncrementModel();
    $generator = new Hashids('test:'.get_class($model));
    $model->save(['id' => 1]);
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeTrue();
    expect($model)->getHashid()->toEqual($generator->encode(1));

    $model = new AutoIncrementModel();
    $model->save(['id' => 13]);
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeTrue();
    expect($model)->getHashid()->toEqual($generator->encode(13));
});

it('can generate hash id with specific minimum length', function () {
    $model = new AutoIncrementModel2();
    $generator = new Hashids('test:'.get_class($model), 5);
    $model->save(['id' => 1]);
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeTrue();
    expect($model)->getHashid()->toEqual($generator->encode(1));
    expect($model)->getHashid()->toHaveLength(5);
});

test('two different models can have same hash id', function () {
    $generator = new Hashids('test');
    $model1 = new TestModel1();
    $model2 = new TestModel2();
    $model1->save(['id' => 1]);
    expect($model1)->getSaved()->toBeTrue();
    expect($model1)->getUpdated()->toBeTrue();
    expect($model1)->getHashid()->toEqual($generator->encode(1));
    $model2->save(['id' => 1]);
    expect($model2)->getSaved()->toBeTrue();
    expect($model2)->getUpdated()->toBeTrue();
    expect($model2)->getHashid()->toEqual($generator->encode(1));

    expect($model1)->getHashid()->toEqual($model2->getHashid());
});


it('should throw invalid option if id is not integer', function () {
    $model = new WithoutHashIdFieldModel();
    $model->save(['id' => 'test']);
})->throws(InvalidOption::class, 'Could not determine in which field the hash id should be saved');

it('should throw invalid option if minimum length is lesser than zero', function () {
    $model = new InvalidMinimumHashLengthModel();
    $model->save(['id' => 'test']);
})->throws(InvalidOption::class, 'Minimum length should be greater than or equal zero');

it('should throw invalid option if type is not equal to "int" or "hex"', function () {
    $model = new InvalidHashTypeModel();
    $model->save(['id' => 'test']);
})->throws(InvalidOption::class, 'Hash id type should be "int" or "hex"');

it('can generate hash id for model with hex type', function () {
    $model = new HexTypeModel();
    $model->id = 1;
    $generator = new Hashids('test:'.get_class($model));
    $model->save();
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeFalse();
    expect($model)->getHashid()->toEqual($generator->encodeHex('1'));

    $model = new HexTypeModel();
    $model->id = new ObjectId('7e6');
    $model->save();
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeFalse();
    expect($model)->getHashid()->toEqual($generator->encodeHex('7e6'));
});

it('can generate hash id for model with custom key', function () {
    $model = new CustomFromKeyModel();
    $model->_id = 100;
    $generator = new Hashids('test:'.get_class($model));
    $model->save();
    expect($model)->getSaved()->toBeTrue();
    expect($model)->getUpdated()->toBeFalse();
    expect($model)->getHashid()->toEqual($generator->encode(100));
});
