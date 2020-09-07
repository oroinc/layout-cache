<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Layout;

use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Bundle\LayoutCacheBundle\Layout\CacheLayoutBuilder;
use Oro\Bundle\LayoutCacheBundle\Layout\LayoutFactoryDecorator;
use Oro\Component\Layout\BlockFactoryInterface;
use Oro\Component\Layout\BlockTypeInterface;
use Oro\Component\Layout\BlockViewCache;
use Oro\Component\Layout\DeferredLayoutManipulatorInterface;
use Oro\Component\Layout\ExpressionLanguage\ExpressionProcessor;
use Oro\Component\Layout\LayoutFactoryInterface;
use Oro\Component\Layout\LayoutRegistryInterface;
use Oro\Component\Layout\LayoutRendererRegistryInterface;
use Oro\Component\Layout\RawLayoutBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LayoutFactoryDecoratorTest extends TestCase
{
    /**
     * @var LayoutFactoryInterface|MockObject
     */
    private $innerLayoutFactory;

    /**
     * @var LayoutFactoryDecorator
     */
    private $layoutFactoryDecorator;

    protected function setUp(): void
    {
        $this->innerLayoutFactory = $this->createMock(LayoutFactoryInterface::class);
        $expressionProcessor = $this->createMock(ExpressionProcessor::class);
        $renderCache = $this->createMock(RenderCache::class);
        $blockViewCache = $this->createMock(BlockViewCache::class);
        $this->layoutFactoryDecorator = new LayoutFactoryDecorator(
            $this->innerLayoutFactory,
            $expressionProcessor,
            $renderCache,
            $blockViewCache
        );
    }

    public function testGetType(): void
    {
        $type = $this->createMock(BlockTypeInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('getType')
            ->with('type name')
            ->willReturn($type);
        $this->assertSame(
            $type,
            $this->layoutFactoryDecorator->getType('type name')
        );
    }

    public function testCreateLayoutManipulator(): void
    {
        $layoutManipulator = $this->createMock(DeferredLayoutManipulatorInterface::class);
        $rawLayoutBuilder = $this->createMock(RawLayoutBuilderInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createLayoutManipulator')
            ->with($rawLayoutBuilder)
            ->willReturn($layoutManipulator);
        $this->assertSame(
            $layoutManipulator,
            $this->layoutFactoryDecorator->createLayoutManipulator($rawLayoutBuilder)
        );
    }

    public function testGetRegistry(): void
    {
        $registry = $this->createMock(LayoutRegistryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('getRegistry')
            ->willReturn($registry);
        $this->assertSame(
            $registry,
            $this->layoutFactoryDecorator->getRegistry()
        );
    }

    public function testCreateRawLayoutBuilder(): void
    {
        $rawLayoutBuilder = $this->createMock(RawLayoutBuilderInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createRawLayoutBuilder')
            ->willReturn($rawLayoutBuilder);
        $this->assertSame(
            $rawLayoutBuilder,
            $this->layoutFactoryDecorator->createRawLayoutBuilder()
        );
    }

    public function testGetRendererRegistry(): void
    {
        $rendererRegistry = $this->createMock(LayoutRendererRegistryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('getRendererRegistry')
            ->willReturn($rendererRegistry);
        $this->assertSame(
            $rendererRegistry,
            $this->layoutFactoryDecorator->getRendererRegistry()
        );
    }

    public function testCreateBlockFactory(): void
    {
        $layoutManipulator = $this->createMock(DeferredLayoutManipulatorInterface::class);
        $blockFactory = $this->createMock(BlockFactoryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createBlockFactory')
            ->with($layoutManipulator)
            ->willReturn($blockFactory);
        $this->assertSame(
            $blockFactory,
            $this->layoutFactoryDecorator->createBlockFactory($layoutManipulator)
        );
    }

    public function testCreateLayoutBuilder(): void
    {
        $layoutManipulator = $this->createMock(DeferredLayoutManipulatorInterface::class);
        $rawLayoutBuilder = $this->createMock(RawLayoutBuilderInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createLayoutManipulator')
            ->with($rawLayoutBuilder)
            ->willReturn($layoutManipulator);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createRawLayoutBuilder')
            ->willReturn($rawLayoutBuilder);
        $registry = $this->createMock(LayoutRegistryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('getRegistry')
            ->willReturn($registry);
        $rendererRegistry = $this->createMock(LayoutRendererRegistryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('getRendererRegistry')
            ->willReturn($rendererRegistry);
        $blockFactory = $this->createMock(BlockFactoryInterface::class);
        $this->innerLayoutFactory->expects($this->once())
            ->method('createBlockFactory')
            ->with($layoutManipulator)
            ->willReturn($blockFactory);

        $this->assertInstanceOf(
            CacheLayoutBuilder::class,
            $this->layoutFactoryDecorator->createLayoutBuilder()
        );
    }
}
