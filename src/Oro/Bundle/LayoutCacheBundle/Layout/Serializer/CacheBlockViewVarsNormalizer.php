<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout\Serializer;

use Oro\Bundle\LayoutBundle\Layout\Serializer\BlockViewVarsNormalizerInterface;

/**
 * Removes "cache" option if its value is NULL.
 */
class CacheBlockViewVarsNormalizer implements BlockViewVarsNormalizerInterface
{
    private const CACHE = 'cache';

    private BlockViewVarsNormalizerInterface $innerNormalizer;

    public function __construct(BlockViewVarsNormalizerInterface $innerNormalizer)
    {
        $this->innerNormalizer = $innerNormalizer;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(array &$vars, array $context): void
    {
        $this->innerNormalizer->normalize($vars, $context);
        if (\array_key_exists(self::CACHE, $vars) && null === $vars[self::CACHE]) {
            unset($vars[self::CACHE]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize(array &$vars, array $context): void
    {
        $this->innerNormalizer->denormalize($vars, $context);
        if (!\array_key_exists(self::CACHE, $vars)) {
            $vars[self::CACHE] = null;
        }
    }
}
