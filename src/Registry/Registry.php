<?php declare(strict_types=1);

namespace Shov\Registry;

use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\RegistryInterface;
use Shov\Registry\Contracts\SaverInterface;
use Shov\Registry\Exceptions\LockedException;
use Shov\Registry\Helpers\PairManagerSupportTrait;
use Shov\Registry\Storage\FakeSaver;

/**
 * The Registry
 */
class Registry implements RegistryInterface
{
    use PairManagerSupportTrait;

    /** @var LoaderInterface */
    protected $loader;

    /** @var SaverInterface */
    protected $saver;

    /** @var array The state */
    protected $pairs = [];

    /** @var array Immutable slice */
    protected $immutablePairs = [];

    /**
     * DI
     * @param LoaderInterface $loader
     * @param SaverInterface $saver
     */
    public function __construct(LoaderInterface $loader, SaverInterface $saver)
    {
        $this->loader = $loader;
        $this->saver = $saver;

        $this->loadState();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        $this->breakIfEmpty($key);
        $this->loadPartial($key);
        return isset($this->immutablePairs[$key]) || isset($this->pairs[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        if (isset($this->immutablePairs[$key])) {
            return $this->immutablePairs[$key];
        }

        $this->loadPartial($key);
        if (isset($this->pairs[$key])) {
            return $this->pairs[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $defaults = []): array
    {
        $this->loadState();
        $all = array_merge($this->pairs, $this->immutablePairs);

        if (!empty($all)) {
            return $all;
        }

        return $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): RegistryInterface
    {
        $this->breakIfEmpty($key);
        $this->breakIfLocked($key);

        $this->pairs[$key] = $value;
        $this->savePartial($key);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function values(array $pairs): RegistryInterface
    {
        $validator = function (string $key, $value) {
            $this->breakIfEmpty($key);
            $this->breakIfLocked($key);
        };

        foreach ($pairs as $key => $value) {
            $validator($key, $value);
        }

        $this->pairs = array_merge($this->pairs, $pairs);
        $this->saveState();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function immutable(string $key, $value): RegistryInterface
    {
        $this->breakIfEmpty($key);
        $this->breakIfLocked($key);

        $this->immutablePairs[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forget(string $key): RegistryInterface
    {
        $this->breakIfEmpty($key);
        $this->breakIfLocked($key);

        unset($this->pairs[$key]);
        $this->savePartial($key);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(bool $force = false): RegistryInterface
    {
        $this->pairs = [];

        if ($force) {
            $this->immutablePairs = [];
        }

        $this->saveState();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImmutableKeys(): array
    {
        return array_keys($this->immutablePairs);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPersist(): RegistryInterface
    {
        $this->saver = new FakeSaver();

        return $this;
    }
}