<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout;

use Oro\Bundle\LayoutCacheBundle\Cache\PlaceholderRenderer;
use Oro\Component\Layout\BlockView;
use Oro\Component\Layout\LayoutRendererInterface;

/**
 * Decorated LayoutRenderer to reset the placeholder cache before the block render.
 */
class LayoutRendererDecorator implements LayoutRendererInterface
{
    /**
     * @var LayoutRendererInterface
     */
    private $inner;

    /**
     * @var PlaceholderRenderer
     */
    private $placeholderRenderer;

    /**
     * @param LayoutRendererInterface $inner
     * @param PlaceholderRenderer     $placeholderRenderer
     */
    public function __construct(
        LayoutRendererInterface $inner,
        PlaceholderRenderer $placeholderRenderer
    ) {
        $this->inner = $inner;
        $this->placeholderRenderer = $placeholderRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock(BlockView $view)
    {
        $this->placeholderRenderer->reset();

        return $this->inner->renderBlock($view);
    }

    /**
     * {@inheritDoc}
     */
    public function setBlockTheme(BlockView $view, $themes)
    {
        return $this->inner->setBlockTheme($view, $themes);
    }

    /**
     * {@inheritDoc}
     */
    public function setFormTheme($themes)
    {
        return $this->inner->setFormTheme($themes);
    }
}
