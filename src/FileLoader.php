<?php namespace TicketEvolution;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

abstract class FileLoader extends BaseFileLoader
{
    public function load($resource, $type = null)
    {
        if (!stream_is_local($resource)) {
            throw new \Exception(sprintf('This is not a local file "%s".', $resource));
        }
        if (!file_exists($resource)) {
            throw new \Exception(sprintf('File "%s" not found.', $resource));
        }

        $configValues = $this->loadResource($resource);

        if (isset($configValues["imports"])) {
            foreach ($configValues["imports"] as $file) {
                $configValues = array_merge_recursive($configValues, $this->import($this->locator->locate($file)));
            }
        }

        unset($configValues["imports"]);

        return $configValues;
    }


    /*
     * @param string $resource
     *
     * @return array
     *
     * @throws InvalidResourceException If stream content has an invalid format.
     */
    abstract protected function loadResource($resource);

}