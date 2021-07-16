<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout;

use Oro\Bundle\LayoutBundle\Form\TwigRendererInterface;
use Oro\Bundle\LayoutCacheBundle\Cache\Metadata\LayoutCacheMetadata;
use Oro\Bundle\LayoutCacheBundle\Cache\PlaceholderRenderer;
use Oro\Bundle\LayoutCacheBundle\Cache\RenderCache;
use Oro\Component\Layout\BlockView;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Form\FormView;
use Twig\Environment;

/**
 * Decorates TwigRenderer to provide caching for layout blocks.
 */
class CacheTwigRendererDecorator implements TwigRendererInterface
{
    /**
     * @var array
     */
    private $blockHierarchy = [];

    /**
     * @var TwigRendererInterface
     */
    private $inner;

    /**
     * @var RenderCache
     */
    private $renderCache;

    /**
     * @var PlaceholderRenderer
     */
    private $placeholderRenderer;

    /**
     * Used to determine when we need to render a placeholder.
     *
     * @var int
     */
    private $cachedBlockNestingLevel = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        TwigRendererInterface $renderer,
        RenderCache $renderCache,
        PlaceholderRenderer $placeholderRenderer,
        LoggerInterface $logger
    ) {
        $this->inner = $renderer;
        $this->renderCache = $renderCache;
        $this->placeholderRenderer = $placeholderRenderer;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @param BlockView $view
     */
    public function searchAndRenderBlock(
        FormView $view,
        $blockNameSuffix,
        array $variables = [],
        $renderParentBlock = false
    ) {
        $metadata = $this->renderCache->getMetadata($view);
        $blockId = $view->vars['id'];

        $isCacheable = $metadata && $this->renderCache->isEnabled();
        if (!$isCacheable) {
            return $this->inner->searchAndRenderBlock($view, $blockNameSuffix, $variables, $renderParentBlock);
        }

        if (!isset($this->blockHierarchy[$blockId]) || $this->blockHierarchy[$blockId] === 0) {
            // INITIAL CALL
            $this->blockHierarchy[$blockId] = 0;
            $this->cachedBlockNestingLevel++;
            $item = $this->renderCache->getItem($view);

            if ($item->isHit()) {
                $html = $item->get();
                $this->logger->debug('Loaded HTML from cache for block "{id}"', ['id' => $blockId]);

                return $this->handlePlaceholders($blockId, $html);
            }
        }
        $this->blockHierarchy[$blockId]++;

        $html = $this->inner->searchAndRenderBlock($view, $blockNameSuffix, $variables, $renderParentBlock);

        if (isset($this->blockHierarchy[$blockId])) {
            $this->blockHierarchy[$blockId]--;
        }
        if ($this->blockHierarchy[$blockId] === 0) {
            // INITIAL CALL
            unset($this->blockHierarchy[$blockId]);
            $this->saveCacheItem($item, $html, $metadata);

            $html = $this->handlePlaceholders($blockId, $html);
        }

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngine()
    {
        return $this->inner->getEngine();
    }

    /**
     * {@inheritDoc}
     */
    public function setTheme(FormView $view, $themes, $useDefaultThemes = true)
    {
        return $this->inner->setTheme($view, $themes, $useDefaultThemes);
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock(FormView $view, $blockName, array $variables = [])
    {
        return $this->inner->renderBlock($view, $blockName, $variables);
    }

    /**
     * {@inheritDoc}
     */
    public function renderCsrfToken($tokenId)
    {
        return $this->inner->renderCsrfToken($tokenId);
    }

    /**
     * {@inheritDoc}
     */
    public function humanize($text)
    {
        return $this->inner->humanize($text);
    }

    /**
     * {@inheritDoc}
     */
    public function setEnvironment(Environment $environment)
    {
        return $this->inner->setEnvironment($environment);
    }

    private function saveCacheItem(CacheItem $item, string $html, LayoutCacheMetadata $metadata): void
    {
        if (0 !== $metadata->getMaxAge()) {
            $item->set($html);
            if (null !== $metadata->getMaxAge()) {
                $item->expiresAfter($metadata->getMaxAge());
            }

            if (!empty($metadata->getTags())) {
                $item->tag($metadata->getTags());
            }

            $this->renderCache->save($item);
        }
    }

    private function handlePlaceholders(string $blockId, string $html): string
    {
        $this->cachedBlockNestingLevel--;

        // Create a placeholder only if it's a nested cached block
        if ($this->cachedBlockNestingLevel > 0) {
            $this->logger->debug(
                'Created a placeholder for block "{id}"',
                ['id' => $blockId, 'cachedBlockNestingLevel' => $this->cachedBlockNestingLevel]
            );

            return $this->placeholderRenderer->createPlaceholder($blockId, $html);
        }

        $this->logger->debug('Rendered placeholders in block "{id}"', ['id' => $blockId]);

        return $this->placeholderRenderer->renderPlaceholders($html);
    }
}
