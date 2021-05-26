<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Cache;

use Oro\Bundle\LayoutCacheBundle\Cache\Extension\RenderCacheExtensionInterface;
use Oro\Bundle\LayoutCacheBundle\Cache\Metadata\CacheMetadataProvider;
use Oro\Bundle\LayoutCacheBundle\Cache\Metadata\LayoutCacheMetadata;
use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Component\Layout\BlockView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RenderCacheTest extends TestCase
{
    /**
     * @var MockObject|TagAwareAdapterInterface
     */
    private $cache;

    /**
     * @var CacheMetadataProvider|MockObject
     */
    private $metadataProvider;

    /**
     * @var MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var RenderCacheExtensionInterface[]
     */
    private $extensions;

    /**
     * @var RenderCache
     */
    private $renderCache;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(TagAwareAdapterInterface::class);
        $this->metadataProvider = $this->createMock(CacheMetadataProvider::class);
        $this->requestStack = new RequestStack();
        $this->extensions = [$this->createMock(RenderCacheExtensionInterface::class)];
        $this->renderCache = new RenderCache(
            $this->cache,
            $this->metadataProvider,
            $this->requestStack,
            $this->extensions
        );
    }

    /**
     * @dataProvider isEnabledProvider
     * @param string $httpMethod
     * @param bool   $enabled
     */
    public function testIsEnabled(string $httpMethod, bool $enabled): void
    {
        $this->requestStack->push(Request::create('', $httpMethod));
        $this->assertEquals($enabled, $this->renderCache->isEnabled());
    }

    /**
     * @return array
     */
    public function isEnabledProvider(): array
    {
        return [
            ['POST', false],
            ['PUT', false],
            ['GET', true],
            ['HEAD', true],
        ];
    }

    public function testIsCached(): void
    {
        $this->requestStack->push(Request::create('', 'GET'));

        $blockView = new BlockView();
        $blockView->vars['id'] = 'block_id';
        $layoutCacheMetadata = new LayoutCacheMetadata();
        $this->metadataProvider->expects($this->exactly(3))
            ->method('getCacheMetadata')
            ->with($blockView)
            ->willReturn($layoutCacheMetadata);

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        $cacheItem->expects($this->any())
            ->method('getKey')
            ->willReturn('block_id');

        $this->cache->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);

        $this->assertTrue($this->renderCache->isCached($blockView));
        // fetch item after isCached must not trigger additional calls of the cache
        $this->renderCache->getItem($blockView);
    }

    public function testGetItem(): void
    {
        $this->requestStack->push(Request::create('', 'GET'));

        $blockView = new BlockView();
        $blockView->vars['id'] = 'block_id';
        $layoutCacheMetadata = new LayoutCacheMetadata();
        $this->metadataProvider->expects($this->once())
            ->method('getCacheMetadata')
            ->with($blockView)
            ->willReturn($layoutCacheMetadata);

        $cacheItem = $this->createMock(CacheItemInterface::class);

        $this->cache->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);

        $this->assertSame(
            $cacheItem,
            $this->renderCache->getItem($blockView)
        );
    }

    public function testSave(): void
    {
        $cacheItem = new CacheItem();

        $this->cache->expects($this->once())
            ->method('save')
            ->with($cacheItem)
            ->willReturn(true);

        $this->assertTrue($this->renderCache->save($cacheItem));
    }

    public function testGetMetadata(): void
    {
        $blockView = new BlockView();
        $blockView->vars['id'] = 'block_id';
        $layoutCacheMetadata = new LayoutCacheMetadata();
        $this->metadataProvider->expects($this->once())
            ->method('getCacheMetadata')
            ->with($blockView)
            ->willReturn($layoutCacheMetadata);
        $this->assertSame(
            $layoutCacheMetadata,
            $this->renderCache->getMetadata($blockView)
        );
    }
}
