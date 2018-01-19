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
     * Make the immutable pair, it don't persist never
     * and will removed after the script terminated
     * @param string $key
     * @param $value
     * @return RegistryInterface
     * @throws LockedException
     */
    public function immutable(string $key, $value): RegistryInterface;

    /**
     * Remove the pair by key
     * @param string $key
     * @return RegistryInterface
     */
    public function forget(string $key): RegistryInterface;

    /**
     * Remove all pairs besides the locked which
     * If $force set true, the immutable pairs will removed as well
     * @param bool $force
     * @return RegistryInterface
     */
    public function flush(bool $force = false): RegistryInterface;

    /**
     * Fetch list of keys of immutable pairs
     * @return array
     */
    public function getImmutableKeys(): array;

    /**
     * Turning off of persisting. After calling this method
     * all pairs (which already set or will set) will store just in php process memory
     * The storage if it was, will stay in state which it was before the method was called
     * @return RegistryInterface
     */
    public function stopPersist(): RegistryInterface;
}