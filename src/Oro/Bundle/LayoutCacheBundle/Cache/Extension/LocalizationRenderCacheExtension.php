<?php

namespace Oro\Bundle\LayoutCacheBundle\Cache\Extension;

use Oro\Bundle\LocaleBundle\Provider\CurrentLocalizationProvider;

/**
 * Render cache extension that adds localization to varyBy cache metadata.
 */
class LocalizationRenderCacheExtension implements RenderCacheExtensionInterface
{
    /**
     * @var CurrentLocalizationProvider
     */
    private $currentLocalizationProvider;

    /**
     * @param CurrentLocalizationProvider $currentLocalizationProvider
     */
    public function __construct(CurrentLocalizationProvider $currentLocalizationProvider)
    {
        $this->currentLocalizationProvider = $currentLocalizationProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function alwaysVaryBy(): array
    {
        $localization = $this->currentLocalizationProvider->getCurrentLocalization();

        if ($localization) {
            return ['localization' => $localization->getId()];
        }

        return [];
    }
}
