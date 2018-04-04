<?php declare(strict_types=1);

namespace Shov\Registry\Helpers;

use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\SaverInterface;
use Shov\Registry\Exceptions\LockedException;

/**
 * Help to manage pairs
 *
 * Required:
 * @property $pairs
 * @property $immutablePairs
 * @property SaverInterface $saver
 * @property LoaderInterface $loader
 */
trait PairManagerSupportTrait
{
    /**
     * Check if key is an empty string, throw an exception
     * @param string $key
     */
    protected function breakIfEmpty(string $key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException("Empty key is invalid key!");
        }
    }

    /**
     * Check if the pair with given key is immutable one then throw the exception
     * @param string $key
     * @throws LockedException
     */
    protected function breakIfLocked(string $key)
    {
        if (isset($this->immutablePairs[$key])) {
            throw new LockedException();
        }
    }

    /**
     * Save the state
     */
    protected function saveState()
    {
        $this->saver->save($this->pairs);
    }

    /**
     * Save given pair if it's possible
     * @param string $key
     */
    protected function savePartial(string $key)
    {
        $this->saver->save($this->pairs, $key);
    }

    /**
     * Load the state
     */
    protected function loadState()
    {
        $this->pairs = $this
            ->loader
            ->load($this->pairs);
    }

    /**
     * Load given pair if it's possible
     * @param string $key
     */
    protected function loadPartial(string $key)
    {
        $this->pairs = $this
            ->loader
            ->load($this->pairs, $key);
    }
}