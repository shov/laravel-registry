<?php declare(strict_types=1);

namespace Shov\Registry\Contracts;

use Shov\Registry\Exceptions\LockedException;

/**
 * The interface for the registry
 */
interface RegistryInterface
{
    /**
     * Check an existing of pair by key
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get the value by key, if value isn't exists $default will be returned
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Fetch all stored pairs, $defaults will be returned instead in a case no pairs are stored
     * @param array $defaults
     * @return array
     */
    public function all(array $defaults = []): array;


    /**
     * Set or rewrite the value by key
     * may throws an exception if you try to set locked by key pair value
     * @param string $key
     * @param $value
     * @return RegistryInterface
     * @throws LockedException
     */
    public function set(string $key, $value): RegistryInterface;

    /**
     * Set several pairs by array of pairs ['key' => 'value', ...]
     * @param array $pairs
     * @return RegistryInterface
     * @throws LockedException
     */
    public function values(array $pairs): RegistryInterface;

    /**
     * Remove the pair by key
     * @param string $key
     * @return RegistryInterface
     */
    public function forget(string $key): RegistryInterface;

    /**
     * Remove all pairs besides the locked which
     * If $force set true, the locked pairs will removed as well
     * @param bool $force
     * @return RegistryInterface
     */
    public function flush(bool $force = false): RegistryInterface;

    /**
     * Mark the pair as the locked by key
     * You can put several keys or ...$arrayOfKeys or even nothing.
     *
     * In case you had call just lock() the method will be trying to lock keys by chain:
     * @example $registry->set('foo', 'bar')->lock(); //foo is locked
     * @example $registry->values(['foo' => 'bar', 'baz' => 42])->lock(); //foo and baz are locked
     *
     * @param string[] ...$keys
     * @return RegistryInterface
     */
    public function lock(string ...$keys): RegistryInterface;

    /**
     * Lock all already set pairs
     * @return RegistryInterface
     */
    public function lockAll(): RegistryInterface;

    /**
     * Fetch all locked pairs' keys
     * @return array
     */
    public function getLockedKeys(): array;

    /**
     * Turning off of persisting. After calling this method
     * all pairs (which already set or will set) will store just in php process memory
     * The storage if it was, will stay in state which it was before the method was called
     * @return RegistryInterface
     */
    public function stopPersist(): RegistryInterface;
}