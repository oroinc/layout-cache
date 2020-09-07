<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache\Extension;

use Oro\Bundle\WebsiteBundle\Manager\WebsiteManager;

/**
 * Render cache extension that adds website to varyBy cache metadata.
 */
class WebsiteRenderCacheExtension implements RenderCacheExtensionInterface
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @param WebsiteManager $websiteManager
     */
    public function __construct(WebsiteManager $websiteManager)
    {
        $this->websiteManager = $websiteManager;
    }

    /**
     * {@inheritDoc}
     */
    public function alwaysVaryBy(): array
    {
        $website = $this->websiteManager->getCurrentWebsite();

        if ($website) {
            return ['website' => $website->getId()];
        }

        return [];
    }
}
