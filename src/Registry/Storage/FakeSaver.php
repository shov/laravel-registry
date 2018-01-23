<?php declare(strict_types=1);

namespace Shov\Registry\Storage;

use Shov\Registry\Contracts\SaverInterface;

/**
 * Save nothing
 */
class FakeSaver implements SaverInterface
{

    /**
     * Save nothing
     * {@inheritdoc}
     */
    public function save(array $pairs)
    {
        ;
    }
}