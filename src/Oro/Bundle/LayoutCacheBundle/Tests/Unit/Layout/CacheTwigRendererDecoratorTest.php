<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Layout;

use Oro\Bundle\LayoutBundle\Form\TwigRendererInterface;
use Oro\Bundle\LayoutCacheBundle\Cache\PlaceholderRenderer;
use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Bundle\LayoutCacheBundle\Layout\CacheTwigRendererDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormRendererEngineInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;

class CacheTwigRendererDecoratorTest extends TestCase
{
    /**
     * @var CacheTwigRendererDecorator
     */
    private $cacheTwigRendererDecorator;

    /**
     * @var TwigRendererInterface|MockObject
     */
    private $innerRenderer;

    protected function setUp(): void
    {
        $this->innerRenderer = $this->createMock(TwigRendererInterface::class);
        $renderCache = $this->createMock(RenderCache::class);
        $placeholderRenderer = $this->createMock(PlaceholderRenderer::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->cacheTwigRendererDecorator = new CacheTwigRendererDecorator(
            $this->innerRenderer,
            $renderCache,
            $placeholderRenderer,
            $logger
        );
    }

    public function testSetEnvironment(): void
    {
        $environment = $this->createMock(Environment::class);
        $this->innerRenderer->expects($this->once())
            ->method('setEnvironment')
            ->with($environment);
        $this->cacheTwigRendererDecorator->setEnvironment($environment);
    }

    public function testHumanize(): void
    {
        $this->innerRenderer->expects($this->once())
            ->method('humanize')
            ->with('some text')
            ->willReturn('humanized text');
        $this->assertEquals(
            'humanized text',
            $this->cacheTwigRendererDecorator->humanize('some text')
        );
    }

    public function testGetEngine(): void
    {
        $engine = $this->createMock(FormRendererEngineInterface::class);
        $this->innerRenderer->expects($this->once())
            ->method('getEngine')
            ->willReturn($engine);
        $this->assertSame(
            $engine,
            $this->cacheTwigRendererDecorator->getEngine()
        );
    }

    public function testRenderCsrfToken(): void
    {
        $this->innerRenderer->expects($this->once())
            ->method('renderCsrfToken')
            ->with('token ID')
            ->willReturn('CsrfToken');
        $this->assertEquals(
            'CsrfToken',
            $this->cacheTwigRendererDecorator->renderCsrfToken('token ID')
        );
    }

    public function testSetTheme(): void
    {
        $view = $this->createMock(FormView::class);
        $themes = ['blank', 'default', 'foo', 'bar'];
        $this->innerRenderer->expects($this->once())
            ->method('setTheme')
            ->with($view, $themes, false)
            ->willReturn(false);
        $this->assertFalse(
            $this->cacheTwigRendererDecorator->setTheme($view, $themes, false)
        );
    }

    public function testRenderBlock(): void
    {
        $view = $this->createMock(FormView::class);
        $vars = ['a' => 5, 'b' => false];
        $this->innerRenderer->expects($this->once())
            ->method('renderBlock')
            ->with($view, 'root', $vars)
            ->willReturn('rendered block');
        $this->assertEquals(
            'rendered block',
            $this->cacheTwigRendererDecorator->renderBlock($view, 'root', $vars)
        );
    }
}
