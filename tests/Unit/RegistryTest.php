<?php

use PHPUnit\Framework\TestCase;
use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\SaverInterface;
use Shov\Registry\Exceptions\LockedException;
use Shov\Registry\Registry;

/**
 * @covers Registry
 */
class RegistryTest extends TestCase
{
    /**
     * @test
     */
    public function has()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';

        //Act
        $hasFoo = $registry->has('foo');
        $hasBar = $registry->has('bar');

        //Assert
        $this->assertTrue($hasFoo);
        $this->assertFalse($hasBar);
    }

    /**
     * @test
     */
    public function hasEmpty()
    {
        //Arrange
        ['registry' => $registry] = $this->mocking();
        /** @var Registry $registry */

        //Expect
        $this->expectException(\Throwable::class);

        //Act
        $registry->has('');
    }

    /**
     * @test
     */
    public function get()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';

        //Act
        $fooVal = $registry->get('foo');
        $barVal = $registry->get('bar');
        $barValOrBanana = $registry->get('bar', 'banana');

        //Assert
        $this->assertSame('apple', $fooVal);
        $this->assertNull($barVal);
        $this->assertSame('banana', $barValOrBanana);
    }

    /**
     * @test
     */
    public function all()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';
        $storage->pairs['bar'] = 'pear';
        $storage->pairs['baz'] = 'pineapple';

        $expectedFull = $storage->pairs;

        $defaultKit = ['bar' => 'banana',];
        $expectedDefault = $defaultKit;

        //Act
        $pairs = $registry->all();
        $pairsFromDefaults = $registry->all($defaultKit);

        //Clean storage
        $storage->pairs = [];

        $pairsFromEmpty = $registry->all();
        $pairsFromEmptyDefaults = $registry->all($defaultKit);

        //Assert
        $this->assertEquals($expectedFull, $pairs);
        $this->assertEquals($expectedFull, $pairsFromDefaults);

        $this->assertEquals([], $pairsFromEmpty);
        $this->assertEquals($expectedDefault, $pairsFromEmptyDefaults);
    }

    /**
     * @test
     * @throws LockedException
     */
    public function set()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';

        $expectPairs = [
            'bar' => 'banana',
            'baz' => 42,
            'foo' => 'pineapple',
        ];

        //Act
        $registry
            ->set('bar', 'banana')
            ->set('baz', 42)
            ->set('foo', 'pineapple');

        //Assert
        $this->assertEquals($expectPairs, $storage->pairs);

        //Expect
        $this->expectException(\Throwable::class);

        //Act
        $registry->set('', 'some-val');
    }

    /**
     * @test
     * @throws LockedException
     */
    public function immutable()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';

        $expectPairs = [
            'bar' => 'banana',
            'baz' => 42,
            'foo' => 'pineapple',
        ];

        //Act
        $registry
            ->immutable('bar', 'banana')
            ->immutable('baz', 42)
            ->immutable('foo', 'pineapple');

        //Assert
        $this->assertEquals($expectPairs, $registry->immutablePairs);
        $this->assertEquals(['foo' => 'apple'], $storage->pairs);

        //Expect
        $this->expectException(\Throwable::class);

        //Act
        $registry->immutable('', 'some-val');
    }

    /**
     * @test
     * @throws LockedException
     */
    public function immutableAndSet()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        //Expect
        $this->expectException(LockedException::class);

        //Act
        $registry
            ->immutable('bar', 'banana')
            ->set('bar', 42);
    }

    /**
     * @test
     * @throws LockedException
     */
    public function immutableAndValues()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        //Expect
        $this->expectException(LockedException::class);

        //Act
        $registry
            ->immutable('bar', 'banana')
            ->values(['bar' => 42, 'baz' => '']);
    }

    /**
     * @test
     * @throws LockedException
     */
    public function values()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs = [
            'bar' => 'banana',
            'baz' => 42,
            'foo' => 'pineapple',
        ];

        $newPairs = [
            'qux' => 11.0,
            'nod' => 'he-he',
            'foo' => 'orange',
        ];

        $expectPairs = array_merge($storage->pairs, $newPairs);

        //Act
        $registry
            ->values($newPairs)
            ->values([]); //we can send an empty array as well

        //Assert
        $this->assertEquals($expectPairs, $storage->pairs);

        //Expect
        $this->expectException(\Throwable::class);

        //Act
        $registry->values([
            'ive no string index',
        ]);
    }

    /**
     * @test
     */
    public function forget()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs['foo'] = 'apple';

        //Act
        $registry->forget('foo');

        //Assert
        $this->assertFalse(isset($storage->pairs['foo']));

        //Expect
        $this->expectException(\Throwable::class);

        //Act
        $registry->forget('');
    }

    /**
     * @test
     */
    public function forgetAndImmutable()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        //Expect
        $this->expectException(LockedException::class);

        //Act
        $registry
            ->immutable('foo', 'apple')
            ->forget('foo');
    }

    /**
     * @test
     */
    public function flush()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs = ['foo' => 'apple', 'bar' => 'orange'];

        //Act
        $registry->flush();

        //Assert
        $this->assertTrue(empty($storage->pairs));
    }

    /**
     * @test
     */
    public function flushAndImmutable()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs = ['baz' => 'banana'];
        $registry->immutablePairs = ['foo' => 'apple'];

        //Act
        $registry->flush();

        //Assert
        $this->assertEmpty($storage->pairs);
        $this->assertEquals(['foo' => 'apple'], $registry->immutablePairs);
    }

    /**
     * @test
     */
    public function flushForceAndImmutable()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs = ['baz' => 'banana'];
        $registry->immutablePairs = ['foo' => 'apple'];

        //Act
        $registry->flush(true);

        //Assert
        $this->assertEmpty($storage->pairs);
        $this->assertEmpty($registry->immutablePairs);
    }

    /**
     * @test
     */
    public function getImmutableKeys()
    {
        //Arrange
        [
            'registry' => $registry,
            'storage' => $storage,

        ] = $this->mocking();
        /** @var Registry $registry */

        $storage->pairs = ['baz' => 'banana'];
        $registry->immutablePairs = ['foo' => 'apple', 'bar' => 'orange'];

        //Act
        $immutableKeys = $registry->getImmutableKeys();

        //Assert
        $this->assertEquals(array_keys($registry->immutablePairs), $immutableKeys);
    }

    protected function mocking()
    {
        $storage = new class () implements SaverInterface, LoaderInterface
        {
            public $pairs = [];

            public function save(array $pairs)
            {
                $this->pairs = $pairs;
            }

            public function load(): array
            {
                return $this->pairs;
            }
        };

        $registry = new class($storage, $storage) extends Registry {
            public $immutablePairs;
        };

        return (compact('storage', 'registry'));
    }
}