<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Layout;

use Oro\Bundle\LayoutCacheBundle\Layout\CacheExpressionProcessor;
use Oro\Component\Layout\ExpressionLanguage\Encoder\ExpressionEncoderRegistry;
use Oro\Component\Layout\ExpressionLanguage\Encoder\JsonExpressionEncoder;
use Oro\Component\Layout\ExpressionLanguage\ExpressionManipulator;
use Oro\Component\Layout\Tests\Unit\ExpressionLanguage\ExpressionProcessorTest;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CacheExpressionProcessorTest extends ExpressionProcessorTest
{
    protected function setUp(): void
    {
        $this->expressionLanguage = new ExpressionLanguage();

        /** @var ExpressionEncoderRegistry|MockObject $encoderRegistry */
        $encoderRegistry = $this->createMock(ExpressionEncoderRegistry::class);

        $this->encoder = new JsonExpressionEncoder(new ExpressionManipulator());

        $encoderRegistry->expects($this->any())
            ->method('get')
            ->with('json')
            ->will($this->returnValue($this->encoder));

        $this->processor = new CacheExpressionProcessor(
            $this->expressionLanguage,
            $encoderRegistry
        );
    }
}
