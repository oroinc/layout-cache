<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout;

use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Component\Layout\BlockViewCache;
use Oro\Component\Layout\DeferredLayoutManipulatorInterface;
use Oro\Component\Layout\ExpressionLanguage\ExpressionProcessor;
use Oro\Component\Layout\LayoutFactoryInterface;
use Oro\Component\Layout\RawLayoutBuilderInterface;

/**
 * Decorates LayoutFactory to override LayoutBuilder with CacheLayoutBuilder.
 */
class LayoutFactoryDecorator implements LayoutFactoryInterface
{
    /**
     * @var LayoutFactoryInterface
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
     * @var RenderCache
     */
    private $renderCache;

    /**
     * @param LayoutFactoryInterface $inner
     * @param ExpressionProcessor    $expressionProcessor
     * @param RenderCache            $renderCache
     * @param BlockViewCache|null    $blockViewCache
     */
    public function __construct(
        LayoutFactoryInterface $inner,
        ExpressionProcessor $expressionProcessor,
        RenderCache $renderCache,
        BlockViewCache $blockViewCache = null
    ) {
        $this->inner = $inner;
        $this->expressionProcessor = $expressionProcessor;
        $this->blockViewCache = $blockViewCache;
        $this->renderCache = $renderCache;
    }

    /**
     * {@inheritDoc}
     */
    public function getRegistry()
    {
        return $this->inner->getRegistry();
    }

    /**
     * {@inheritDoc}
     */
    public function getRendererRegistry()
    {
        return $this->inner->getRendererRegistry();
    }

    /**
     * {@inheritDoc}
     */
    public function getType($name)
    {
        return $this->inner->getType($name);
    }

    /**
     * {@inheritDoc}
     */
    public function createRawLayoutBuilder()
    {
        return $this->inner->createRawLayoutBuilder();
    }

    /**
     * {@inheritDoc}
     */
    public function createLayoutManipulator(RawLayoutBuilderInterface $rawLayoutBuilder)
    {
        return $this->inner->createLayoutManipulator($rawLayoutBuilder);
    }

    /**
     * {@inheritDoc}
     */
    public function createBlockFactory(DeferredLayoutManipulatorInterface $layoutManipulator)
    {
        return $this->inner->createBlockFactory($layoutManipulator);
    }

    /**
     * {@inheritDoc}
     */
    public function createLayoutBuilder()
    {
        $rawLayoutBuilder = $this->createRawLayoutBuilder();
        $layoutManipulator = $this->createLayoutManipulator($rawLayoutBuilder);
        return new CacheLayoutBuilder(
            $this->getRegistry(),
            $rawLayoutBuilder,
            $layoutManipulator,
            $this->createBlockFactory($layoutManipulator),
            $this->getRendererRegistry(),
            $this->expressionProcessor,
            $this->renderCache,
            $this->blockViewCache
        );
    }
}
