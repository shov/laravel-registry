<?php declare(strict_types=1);

namespace Shov\Registry\Contracts;

/**
 * Save the pairs to some storage or something like that
 */
interface SaverInterface
{
    /**
     * Save all given pairs
     * @param array $pairs @example ['key' => 'value', ...]
     * @param string|null $key : make accent on current key saving
     * @return mixed
     */
    public function save(array $pairs, $key = null);
}