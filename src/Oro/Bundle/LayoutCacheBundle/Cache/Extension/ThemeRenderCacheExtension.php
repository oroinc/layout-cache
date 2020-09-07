<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache\Extension;

use Oro\Bundle\LayoutBundle\Layout\LayoutContextHolder;

/**
 * Render cache extension that adds theme to varyBy cache metadata.
 */
class ThemeRenderCacheExtension implements RenderCacheExtensionInterface
{
    /**
     * @var LayoutContextHolder
     */
    private $contextHolder;

    /**
     * @param LayoutContextHolder $contextHolder
     */
    public function __construct(LayoutContextHolder $contextHolder)
    {
        $this->contextHolder = $contextHolder;
    }

    /**
     * {@inheritDoc}
     */
    public function alwaysVaryBy(): array
    {
        return ['theme' => $this->contextHolder->getContext()->get('theme')];
    }
}
