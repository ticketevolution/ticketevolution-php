<?php

declare(strict_types=1);

namespace TicketEvolution;

class PhpLoader extends FileLoader
{
    public function loadResource($resource): array
    {
        return require $resource;
    }

    public function supports($resource, $type = null): bool
    {
        return is_string($resource) && ('php' === pathinfo(
                    path: $resource,
                    flags: PATHINFO_EXTENSION
                ));
    }
}
