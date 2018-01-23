<?php declare(strict_types=1);

namespace Shov\Registry\Storage;

use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\SaverInterface;

/**
 * Store pairs into internal storage
 */
class MemoryStorage implements SaverInterface, LoaderInterface
{
    /** @var array */
    protected $internalStorage = [];

    /**
     * Load internal stored
     * {@inheritdoc}
     */
    public function load(): array
    {
        return $this->internalStorage;
    }

    /**
     * Save in internal storage
     * {@inheritdoc}
     */
    public function save(array $pairs)
    {
        $this->internalStorage = $pairs;
    }
}