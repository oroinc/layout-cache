<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Layout;

use Oro\Bundle\LayoutCacheBundle\Cache\Metadata\CacheMetadataProvider;
use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Bundle\LayoutCacheBundle\Layout\LayoutFactoryBuilderDecorator;
use Oro\Bundle\LayoutCacheBundle\Layout\LayoutFactoryDecorator;
use Oro\Component\Layout\BlockTypeExtensionInterface;
use Oro\Component\Layout\BlockTypeInterface;
use Oro\Component\Layout\BlockViewCache;
use Oro\Component\Layout\ExpressionLanguage\ExpressionProcessor;
use Oro\Component\Layout\Extension\ExtensionInterface;
use Oro\Component\Layout\LayoutFactoryBuilderInterface;
use Oro\Component\Layout\LayoutFactoryInterface;
use Oro\Component\Layout\LayoutRendererInterface;
use Oro\Component\Layout\LayoutUpdateInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LayoutFactoryBuilderDecoratorTest extends TestCase
{
    /**
     * @var LayoutFactoryBuilderDecorator
     */
    private $layoutFactoryBuilderDecorator;

    /**
     * @var LayoutFactoryBuilderInterface|MockObject
     */
    private $innerBuilder;

    /**
     * @var CacheMetadataProvider|MockObject
     */
    private $cacheMetadataProvider;

    protected function setUp(): void
    {
        $this->innerBuilder = $this->createMock(LayoutFactoryBuilderInterface::class);
        $renderCache = $this->createMock(RenderCache::class);
        $this->cacheMetadataProvider = $this->createMock(CacheMetadataProvider::class);
        $expressionProcessor = $this->createMock(ExpressionProcessor::class);
        $blockViewCache = $this->createMock(BlockViewCache::class);
        $debug = true;
        $this->layoutFactoryBuilderDecorator = new LayoutFactoryBuilderDecorator(
            $this->innerBuilder,
            $renderCache,
            $this->cacheMetadataProvider,
            $expressionProcessor,
            $blockViewCache,
            $debug
        );
    }

    public function testAddRenderer(): void
    {
        $renderer = $this->createMock(LayoutRendererInterface::class);
        $this->innerBuilder->expects($this->once())
            ->method('addRenderer')
            ->with('renderer name', $renderer);
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->addRenderer('renderer name', $renderer)
        );
    }

    public function testAddTypeExtension(): void
    {
        $extension = $this->createMock(BlockTypeExtensionInterface::class);
        $this->innerBuilder->expects($this->once())
            ->method('addTypeExtension')
            ->with($extension);
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->addTypeExtension($extension)
        );
    }

    public function testAddLayoutUpdate(): void
    {
        $layoutUpdate = $this->createMock(LayoutUpdateInterface::class);
        $this->innerBuilder->expects($this->once())
            ->method('addLayoutUpdate')
            ->with('layout update id', $layoutUpdate);
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->addLayoutUpdate('layout update id', $layoutUpdate)
        );
    }

    public function testAddExtension(): void
    {
        $extension = $this->createMock(ExtensionInterface::class);
        $this->innerBuilder->expects($this->once())
            ->method('addExtension')
            ->with($extension);
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->addExtension($extension)
        );
    }

    public function testAddType(): void
    {
        $type = $this->createMock(BlockTypeInterface::class);
        $this->innerBuilder->expects($this->once())
            ->method('addType')
            ->with($type);
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->addType($type)
        );
    }

    public function testSetDefaultRenderer(): void
    {
        $this->innerBuilder->expects($this->once())
            ->method('setDefaultRenderer')
            ->with('default renderer name');
        $this->assertSame(
            $this->layoutFactoryBuilderDecorator,
            $this->layoutFactoryBuilderDecorator->setDefaultRenderer('default renderer name')
        );
    }

    public function testGetLayoutFactory(): void
    {
        $layoutFactory = $this->createMock(LayoutFactoryInterface::class);
        $this->cacheMetadataProvider->expects($this->once())
            ->method('reset');
        $this->innerBuilder->expects($this->once())
            ->method('getLayoutFactory')
            ->willReturn($layoutFactory);
        $this->assertInstanceOf(
            LayoutFactoryDecorator::class,
            $this->layoutFactoryBuilderDecorator->getLayoutFactory()
        );
    }
}
