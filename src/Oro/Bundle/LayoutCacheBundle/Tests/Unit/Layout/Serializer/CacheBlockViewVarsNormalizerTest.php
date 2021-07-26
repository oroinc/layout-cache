<?php

namespace Oro\Bundle\LayoutCacheBundle\Tests\Unit\Layout\Serializer;

use Oro\Bundle\LayoutBundle\Layout\Serializer\BlockViewVarsNormalizerInterface;
use Oro\Bundle\LayoutCacheBundle\Layout\Serializer\CacheBlockViewVarsNormalizer;

class CacheBlockViewVarsNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var BlockViewVarsNormalizerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerNormalizer;

    /** @var CacheBlockViewVarsNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->innerNormalizer = $this->createMock(BlockViewVarsNormalizerInterface::class);

        $this->normalizer = new CacheBlockViewVarsNormalizer($this->innerNormalizer);
    }

    /**
     * @dataProvider varsDataProvider
     */
    public function testNormalize(array $vars, array $normalizedVars): void
    {
        $context = ['key' => 'value'];

        $this->innerNormalizer->expects(self::once())
            ->method('normalize')
            ->with($vars, $context);

        $this->normalizer->normalize($vars, $context);
        self::assertEquals($normalizedVars, $vars);
    }

    /**
     * @dataProvider varsDataProvider
     */
    public function testDenormalize(array $vars, array $normalizedVars): void
    {
        $context = ['key' => 'value'];

        $this->innerNormalizer->expects(self::once())
            ->method('denormalize')
            ->with($normalizedVars, $context);

        $this->normalizer->denormalize($normalizedVars, $context);
        self::assertEquals($vars, $normalizedVars);
    }

    public function varsDataProvider(): array
    {
        return [
            'null cache'      => [
                'vars'           => [
                    'option1' => 'value1',
                    'cache'   => null
                ],
                'normalizedVars' => [
                    'option1' => 'value1'
                ]
            ],
            'empty cache'     => [
                'vars'           => [
                    'option1' => 'value1',
                    'cache'   => ''
                ],
                'normalizedVars' => [
                    'option1' => 'value1',
                    'cache'   => ''
                ]
            ],
            'not empty cache' => [
                'vars'           => [
                    'option1' => 'value1',
                    'cache'   => 'test'
                ],
                'normalizedVars' => [
                    'option1' => 'value1',
                    'cache'   => 'test'
                ]
            ],
        ];
    }

    public function testNormalizeNoCache(): void
    {
        $context = ['key' => 'value'];
        $vars = [
            'option1' => 'value1'
        ];
        $normalizedVars = [
            'option1' => 'value1'
        ];

        $this->innerNormalizer->expects(self::once())
            ->method('normalize')
            ->with($vars, $context);

        $this->normalizer->normalize($vars, $context);
        self::assertEquals($normalizedVars, $vars);
    }
}
