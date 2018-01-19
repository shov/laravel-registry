<?php declare(strict_types=1);

namespace Shov\Registry;

use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\RegistryInterface;
use Shov\Registry\Contracts\SaverInterface;
use Shov\Registry\Exceptions\LockedException;

/**
 * The Registry
 */
class Registry implements RegistryInterface
{
    /** @var LoaderInterface */
    protected $loader;

    /** @var SaverInterface */
    protected $saver;

    /**
     * DI
     * @param LoaderInterface $loader
     * @param SaverInterface $saver
     */
    public function __construct(LoaderInterface $loader, SaverInterface $saver)
    {
        $this->loader = $loader;
        $this->saver = $saver;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        // TODO: Implement has() method.
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $defaults = []): array
    {
        // TODO: Implement all() method.
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): RegistryInterface
    {
        // TODO: Implement set() method.
    }

    /**
     * {@inheritdoc}
     */
    public function values(array $pairs): RegistryInterface
    {
        // TODO: Implement values() method.
    }

    /**
     * {@inheritdoc}
     */
    public function immutable(string $key, $value): RegistryInterface
    {
        // TODO: Implement immutable() method.
    }

    /**
     * {@inheritdoc}
     */
    public function forget(string $key): RegistryInterface
    {
        // TODO: Implement forget() method.
    }

    /**
     * {@inheritdoc}
     */
    public function flush(bool $force = false): RegistryInterface
    {
        // TODO: Implement flush() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableKeys(): array
    {
        // TODO: Implement getImmutableKeys() method.
    }

    /**
     * {@inheritdoc}
     */
    public function stopPersist(): RegistryInterface
    {
        // TODO: Implement stopPersist() method.
    }
}