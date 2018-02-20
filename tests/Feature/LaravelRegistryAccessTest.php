<?php declare(strict_types=1);

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\TestCase;


class LaravelRegistryAccessTest extends TestCase
{
    use CreatesApplication;
    /**
     * @test
     * @dataProvider behaviourDataProvider
     */
    public function dependencyInjection($cmd)
    {
        //Arrange
        /** @var RegistryUser $ru */
        $ru = App::make(RegistryUser::class);

        //Act
        $result = [];

        switch ($cmd) {
            case 'set':
                $ru->setVars();
            case 'read':
                $result = $ru->getVars();
                break;
            case 'flush':
                \Shov\Registry\Facades\Registry::flush();
                break;
        }

        //Arrange

        if('flush' === $cmd) {
            $this->assertSame($result, []);
        } else {
            $this->assertSame($result, [
                'a' => 1,
                'b' => 3,
                'foo' => 'baz',
            ]);
        }
    }

    public function behaviourDataProvider()
    {
        return [['set', 'read', 'read', 'flush',]];
    }
}

class RegistryUser
{
    /** @var \Shov\Registry\Registry */
    protected $registry;

    /**
     * DI
     */
    public function __construct(\Shov\Registry\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function setVars()
    {
        $this->registry
            ->values([
                'a' => 1,
                'b' => 3,
                'foo' => 'baz',
            ]);
    }

    public function getVars(): array
    {
        return $this->registry->all();
    }
}