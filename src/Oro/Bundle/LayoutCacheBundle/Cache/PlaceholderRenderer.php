<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache;

use Oro\Bundle\LayoutBundle\Layout\LayoutContextHolder;
use Oro\Bundle\LayoutBundle\Layout\LayoutManager;
use Psr\Log\LoggerInterface;

/**
 * Renderer for cache placeholders used for post-cache substitution.
 */
class PlaceholderRenderer
{
    /**
     * @var LayoutManager
     */
    private $layoutManager;

    /**
     * @var LayoutContextHolder
     */
    private $contextHolder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $renderedPlaceholders = [];

    /**
     * @param LayoutManager       $layoutManager
     * @param LayoutContextHolder $contextHolder
     * @param LoggerInterface     $logger
     */
    public function __construct(
        LayoutManager $layoutManager,
        LayoutContextHolder $contextHolder,
        LoggerInterface $logger
    ) {
        $this->layoutManager = $layoutManager;
        $this->contextHolder = $contextHolder;
        $this->logger = $logger;
    }

    /**
     * @param string $blockId
     * @param string $html
     * @return string
     */
    public function createPlaceholder(string $blockId, string $html): string
    {
        $this->renderedPlaceholders[$blockId] = $html;

        return $this->getPlaceholder($blockId);
    }

    /**
     * @param string $blockId
     * @return string
     */
    private function getPlaceholder(string $blockId): string
    {
        return '<!-- PLACEHOLDER '.$blockId.' -->';
    }

    /**
     * @param string $html
     * @return string
     */
    public function renderPlaceholders(string $html): string
    {
        $blockIds = $this->getPlaceholderBlockIds($html);

        if (!$blockIds) {
            return $html;
        }

        $placeholders = [];
        foreach ($blockIds as $blockId) {
            $blockHtml = $this->renderPlaceholderContent($blockId);
            $placeholder = $this->getPlaceholder($blockId);
            $placeholders[$placeholder] = $blockHtml;
        }
        $this->logger->debug('Rendered placeholders', ['ids' => $blockIds]);

        return strtr($html, $placeholders);
    }

    /**
     * @param string $html
     * @return string[]
     */
    private function getPlaceholderBlockIds(string $html): array
    {
        preg_match_all('/<\!--\ PLACEHOLDER\ ([a-z][a-z0-9\_\-\:]+)\ -->/i', $html, $matches);

        return $matches[1];
    }

    /**
     * @param string $blockId
     * @return string
     */
    private function renderPlaceholderContent(string $blockId): string
    {
        if (isset($this->renderedPlaceholders[$blockId])) {
            $html = $this->renderedPlaceholders[$blockId];
            // handle nested placeholders
            return $this->renderPlaceholders($html);
        }
        $context = $this->contextHolder->getContext();

        return $this->layoutManager->getLayout($context, $blockId)->render();
    }

    public function reset()
    {
        $this->renderedPlaceholders = [];
    }
}
