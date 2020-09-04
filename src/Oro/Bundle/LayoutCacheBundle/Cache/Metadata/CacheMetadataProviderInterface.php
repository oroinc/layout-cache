<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache\Metadata;

use Oro\Component\Layout\BlockView;
use Oro\Component\Layout\ContextInterface;

/**
 * An interface that is used to provide layout cache metadata from PHP.
 */
interface CacheMetadataProviderInterface
{
    /**
     * Return cache metadata for the layout block when it must be cached, null otherwise
     *
     * @param BlockView        $blockView
     * @param ContextInterface $context
     * @return LayoutCacheMetadata|null
     */
    public function getCacheMetadata(BlockView $blockView, ContextInterface $context): ?LayoutCacheMetadata;
}
