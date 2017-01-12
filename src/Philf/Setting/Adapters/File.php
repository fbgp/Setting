<?php namespace Philf\Setting\Adapters;

use Philf\Setting\interfaces\AdapterInterface;

class File implements AdapterInterface
{
    public function load($configName) : string
    {
        $config = '';

        if (is_file($configName))
        {
            $config = file_get_contents($configName);
        }

        return $config;
    }

    public function save($configName, $contents)
    {
        $path = dirname($configName);

        if (!@mkdir($path, 0755, true) && (!file_exists($configName) && !is_dir($path)))
        {
                throw new \ErrorException('Destination path seems to be not writable ' . $path);
        }

        $fh = fopen($configName, 'wb+');
        fwrite($fh, $contents);
        fclose($fh);
    }
}