<?php declare(strict_types=1);

namespace Shov\Registry\Storage;

use Illuminate\Support\Facades\Storage;
use Shov\Registry\Contracts\LoaderInterface;
use Shov\Registry\Contracts\SaverInterface;

/**
 * Store in files with @see Storage
 */
class FileStorage implements SaverInterface, LoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function load(): array
    {
        $this->checkFile();
        $content = Storage::get($this->fileName());

        return json_decode($content, true);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $pairs)
    {
        $content = json_encode($pairs);
        Storage::put($this->fileName(), $content);
    }

    /**
     * Get name of file in storage
     * TODO: move to config
     * @return string
     */
    protected function fileName(): string
    {
        return 'Shov_Registry/registry.json';
    }

    protected function checkFile()
    {
        if (!Storage::exists($this->fileName())) {
            Storage::put($this->fileName(), json_encode([]));
        }
    }
}