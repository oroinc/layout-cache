<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache\Extension;

/**
 * An interface that is used to extend varyBy cache metadata for all the blocks.
 */
interface RenderCacheExtensionInterface
{
    public function alwaysVaryBy(): array;
}
