<?php

use PHPUnit\Framework\TestCase;
use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\SaverInterface;
use Shov\Registry\Exceptions\LockedException;
use Shov\Registry\Registry;

class RegistryTest extends TestCase
{
    /**
     * @test
     */
    public function has()
    {
        //Arrange
        $storage = $this->mockStorage();
        $storage->pairs['foo'] = 'apple';

        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();

        $registry = $this->mockRegistry($storage, $storage);

        //Expect
        $this->expectException(\InvalidArgumentException::class);

        //Act
        $registry->has('');
    }

    /**
     * @test
     */
    public function get()
    {
        //Arrange
        $storage = $this->mockStorage();
        $storage->pairs['foo'] = 'apple';

        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $storage->pairs['foo'] = 'apple';
        $storage->pairs['bar'] = 'pear';
        $storage->pairs['baz'] = 'pineapple';

        $registry = $this->mockRegistry($storage, $storage);

        $expectedFull = $storage->pairs;

        $defaultKit = ['bar' => 'banana',];
        $expectedDefault = $defaultKit;

        //Act
        $pairs = $registry->all();
        $pairsFromDefaults = $registry->all($defaultKit);

        //Clean storage
        $storage->pairs = [];
        $registry = $this->mockRegistry($storage, $storage);

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
     */
    public function immutableAndAll()
    {
        //Arrange
        $storage = $this->mockStorage();
        $storage->pairs['foo'] = 'apple';
        $storage->pairs['bar'] = 'pear';
        $storage->pairs['baz'] = 'pineapple';

        $registry = $this->mockRegistry($storage, $storage);

        $registry->setImmutablePairs(['zex' => 'apple']);

        $expectedFull = array_merge($storage->pairs, $registry->getImmutablePairs());

        $defaultKit = ['bar' => 'banana',];
        $expectedDefault = $defaultKit;

        //Act
        $pairs = $registry->all();
        $pairsFromDefaults = $registry->all($defaultKit);

        //Clean storagef
        $storage->pairs = [];
        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $this->expectException(\InvalidArgumentException::class);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $this->assertEquals($expectPairs, $registry->getImmutablePairs());
        $this->assertEquals(['foo' => 'apple'], $storage->pairs);

        //Expect
        $this->expectException(\InvalidArgumentException::class);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $storage->pairs = [
            'bar' => 'banana',
            'baz' => 42,
            'foo' => 'pineapple',
        ];

        $registry = $this->mockRegistry($storage, $storage);

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
        $this->expectException(\TypeError::class);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

        $storage->pairs['foo'] = 'apple';

        //Act
        $registry->forget('foo');

        //Assert
        $this->assertFalse(isset($storage->pairs['foo']));

        //Expect
        $this->expectException(\InvalidArgumentException::class);

        //Act
        $registry->forget('');
    }

    /**
     * @test
     */
    public function forgetAndImmutable()
    {
        //Arrange
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

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
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

        $storage->pairs = ['baz' => 'banana'];
        $registry->setImmutablePairs(['foo' => 'apple']);

        //Act
        $registry->flush();

        //Assert
        $this->assertEmpty($storage->pairs);
        $this->assertEquals(['foo' => 'apple'], $registry->getImmutablePairs());
    }

    /**
     * @test
     */
    public function flushForceAndImmutable()
    {
        //Arrange
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

        $storage->pairs = ['baz' => 'banana'];
        $registry->setImmutablePairs(['foo' => 'apple']);

        //Act
        $registry->flush(true);

        //Assert
        $this->assertEmpty($storage->pairs);
        $this->assertEmpty($registry->getImmutablePairs());
    }

    /**
     * @test
     */
    public function getImmutableKeys()
    {
        //Arrange
        $storage = $this->mockStorage();
        $registry = $this->mockRegistry($storage, $storage);

        $storage->pairs = ['baz' => 'banana'];
        $registry->setImmutablePairs(['foo' => 'apple', 'bar' => 'orange']);

        //Act
        $immutableKeys = $registry->getImmutableKeys();

        //Assert
        $this->assertEquals(array_keys($registry->getImmutablePairs()), $immutableKeys);
    }

    protected function mockStorage()
    {
        return new class () implements SaverInterface, LoaderInterface
        {
            public $pairs = [];

            public function save(array $pairs, $key = null)
            {
                $this->pairs = $pairs;
            }

            public function load(array $pairs, $key = null): array
            {
                return $this->pairs;
            }
        };
    }

    protected function mockRegistry(LoaderInterface $loader, SaverInterface $saver)
    {
        return new class($loader, $saver) extends Registry
        {
            public function getImmutablePairs():array
            {
                return $this->immutablePairs;
            }

            public function setImmutablePairs(array $immutablePairs)
            {
                $this->immutablePairs = $immutablePairs;
            }
        };
    }
}