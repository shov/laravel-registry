<?php declare(strict_types=1);

namespace Shov\Registry\Facades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\RegistryInterface;
use Shov\Registry\Contracts\SaverInterface;

/**
 * Facade for Registry instance @see \Shov\Registry\Registry
 *
 * @method static __construct(LoaderInterface $loader, SaverInterface $saver)
 * @method static bool has(string $key)
 * @method static get(string $key, $default = null)
 * @method static array all(array $defaults = [])
 * @method static RegistryInterface set(string $key, $value)
 * @method static RegistryInterface values(array $pairs)
 * @method static RegistryInterface immutable(string $key, $value)
 * @method static RegistryInterface forget(string $key)
 * @method static RegistryInterface flush(bool $force = false)
 * @method static array getImmutableKeys()
 * @method static RegistryInterface stopPersist()
 */
class Registry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return App::make(\Shov\Registry\Registry::class);
    }
}