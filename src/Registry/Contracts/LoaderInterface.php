<?php declare(strict_types=1);

namespace Shov\Registry\Contracts;

/**
 * Load the pairs from some storage or something like that
 */
interface LoaderInterface
{
    /**
     * Load all pairs as array
     * @return array @example ['key' => 'value', ...]
     */
    public function load(): array;
}