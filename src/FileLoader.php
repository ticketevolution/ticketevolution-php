<?php

declare(strict_types=1);

namespace TicketEvolution;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

abstract class FileLoader extends BaseFileLoader
{
    public function load($resource, $type = null)
    {
        if (! stream_is_local($resource)) {
            throw new \RuntimeException(sprintf('This is not a local file "%s".', $resource));
        }
        if (! file_exists($resource)) {
            throw new \RuntimeException(sprintf('File "%s" not found.', $resource));
        }

        $configValues = $this->loadResource($resource);

        if (isset($configValues['imports'])) {
            foreach ($configValues['imports'] as $file) {
                $configValues = array_merge_recursive(
                    $configValues,
                    $this->import($this->locator->locate($file))
                );
            }
        }

        unset($configValues['imports']);

        return $configValues;
    }

    abstract protected function loadResource(string $resource): array;
}
