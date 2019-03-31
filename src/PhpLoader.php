<?php namespace TicketEvolution;

class PhpLoader extends FileLoader
{
    public function loadResource($resource)
    {
        return require $resource;
    }


    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}