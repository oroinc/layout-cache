<?php

namespace Oro\Bundle\LayoutCacheBundle\Layout;

use InvalidArgumentException;
use Oro\Component\Layout\ContextInterface;
use Oro\Component\Layout\DataAccessorInterface;
use Oro\Component\Layout\ExpressionLanguage\ExpressionProcessor;
use Symfony\Component\ExpressionLanguage\ParsedExpression;

/**
 * Overrides ExpressionProcessor to not process expression in options of cached blocks.
 */
class CacheExpressionProcessor extends ExpressionProcessor
{
    /**
     * @var bool
     */
    private $cached = false;

    /**
     * {@inheritDoc}
     */
    public function processExpressions(
        array &$values,
        ContextInterface $context,
        DataAccessorInterface $data = null,
        $evaluate = true,
        $encoding = null
    ) {
        if (!$evaluate && $encoding === null) {
            return;
        }
        if (isset($values['data']) || isset($values['context'])) {
            throw new InvalidArgumentException('"data" and "context" should not be used as value keys.');
        }
        $this->values = $values;
        $this->processingValues = [];
        $this->processedValues = [];

        if (array_key_exists('visible', $values)) {
            $this->visible = $values['visible'];
            $this->processRootValue('visible', $this->visible, $context, $data, $evaluate, $encoding);
        }

        $this->cached = array_key_exists('_cached', $values) && $values['_cached'];

        foreach ($values as $key => $value) {
            if (!array_key_exists($key, $this->processedValues)) {
                $this->processRootValue($key, $value, $context, $data, $evaluate, $encoding);
            } else {
                $value = $this->processedValues[$key];
            }
            $values[$key] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function processExpression(
        ParsedExpression $expr,
        ContextInterface $context,
        DataAccessorInterface $data = null,
        $evaluate = true,
        $encoding = null
    ) {
        if (true === $this->cached) {
            return null;
        }

        return parent::processExpression($expr, $context, $data, $evaluate, $encoding);
    }
}
