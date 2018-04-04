<?php declare(strict_types=1);

namespace Shov\Registry\Contracts;

/**
 * Load the pairs from some storage or something like that
 */
interface LoaderInterface
{
    /**
     * Load all pairs as array
     * @param array
     *      $pairs @example ['key' => 'value', ...]
     *      can be useful if loading based on the state
     *
     * @param string|null $key : make accent on wanted value
     * @return array @example ['key' => 'value', ...]
     */
    public function load(array $pairs, $key = null): array;
}