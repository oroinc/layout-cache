<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout;

use Oro\Bundle\LayoutCacheBundle\Cache\Metadata\CacheMetadataProvider;
use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Component\Layout\BlockTypeExtensionInterface;
use Oro\Component\Layout\BlockTypeInterface;
use Oro\Component\Layout\BlockViewCache;
use Oro\Component\Layout\ExpressionLanguage\ExpressionProcessor;
use Oro\Component\Layout\Extension\ExtensionInterface;
use Oro\Component\Layout\LayoutFactoryBuilderInterface;
use Oro\Component\Layout\LayoutRendererInterface;
use Oro\Component\Layout\LayoutUpdateInterface;

/**
 * Decorates LayoutFactoryBuilder to override LayoutFactory with LayoutFactoryDecorator
 * and to reset metadata cache before layout factory creation.
 */
class LayoutFactoryBuilderDecorator implements LayoutFactoryBuilderInterface
{
    /**
     * @var LayoutFactoryBuilderInterface
     */
    private $inner;

    /**
     * @var ExpressionProcessor
     */
    private $expressionProcessor;

    /**
     * @var BlockViewCache|null
     */
    private $blockViewCache;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var RenderCache
     */
    private $renderCache;

    /**
     * @var CacheMetadataProvider
     */
    private $cacheMetadataProvider;

    public function __construct(
        LayoutFactoryBuilderInterface $inner,
        RenderCache $renderCache,
        CacheMetadataProvider $cacheMetadataProvider,
        ExpressionProcessor $expressionProcessor,
        BlockViewCache $blockViewCache = null,
        bool $debug = false
    ) {
        $this->inner = $inner;
        $this->expressionProcessor = $expressionProcessor;
        $this->blockViewCache = $blockViewCache;
        $this->debug = $debug;
        $this->renderCache = $renderCache;
        $this->cacheMetadataProvider = $cacheMetadataProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->inner->addExtension($extension);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addType(BlockTypeInterface $type)
    {
        $this->inner->addType($type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTypeExtension(BlockTypeExtensionInterface $typeExtension)
    {
        $this->inner->addTypeExtension($typeExtension);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addLayoutUpdate($id, LayoutUpdateInterface $layoutUpdate)
    {
        $this->inner->addLayoutUpdate($id, $layoutUpdate);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addRenderer($name, LayoutRendererInterface $renderer)
    {
        $this->inner->addRenderer($name, $renderer);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultRenderer($name)
    {
        $this->inner->setDefaultRenderer($name);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLayoutFactory()
    {
        $this->cacheMetadataProvider->reset();

        return new LayoutFactoryDecorator(
            $this->inner->getLayoutFactory(),
            $this->expressionProcessor,
            $this->renderCache,
            $this->getBlockViewCache()
        );
    }

    private function getBlockViewCache(): ?BlockViewCache
    {
        return $this->debug === false ? $this->blockViewCache : null;
    }
}
