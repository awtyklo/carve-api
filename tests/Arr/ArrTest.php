<?php

declare(strict_types=1);

namespace tests\Arr;

use Carve\ApiBundle\Helper\Arr;
use PHPUnit\Framework\TestCase;

/**
 * Testing App\Helper\Arr class.
 */
class ArrTest extends TestCase
{
    public function testAccessible()
    {
        $this->assertTrue(Arr::accessible([]));
        $this->assertFalse(Arr::accessible(false));
    }

    public function testExists()
    {
        $array = [
            'a1' => [
                'b1' => [
                    'c1' => 0,
                ],
            ],
            'a2' => [
                'b2' => [
                    'c2' => false,
                ],
            ],
            'a3' => [
                'b3' => [
                    'c3' => null,
                ],
            ],
            'a4' => [
                'b4' => [
                    'c4' => ['item'],
                ],
            ],
        ];

        $this->assertTrue(Arr::exists($array, 'a1'));
        $this->assertFalse(Arr::exists($array, 'a0'));

        // Arr::exists() does not support dot notation
        $this->assertFalse(Arr::exists($array, 'a1.b1'));
        $this->assertFalse(Arr::exists($array, 'a1.b1.c1'));
    }

    public function testHas()
    {
        $array = [
            'a1' => [
                'b1' => [
                    'c1' => 0,
                ],
            ],
            'a2' => [
                'b2' => [
                    'c2' => false,
                ],
            ],
            'a3' => [
                'b3' => [
                    'c3' => null,
                ],
            ],
            'a4' => [
                'b4' => [
                    'c4' => ['item'],
                ],
            ],
        ];

        $this->assertTrue(Arr::has($array, 'a1'));
        $this->assertFalse(Arr::has($array, 'a0'));

        $this->assertTrue(Arr::has($array, 'a1.b1'));
        $this->assertFalse(Arr::has($array, 'a0.b1'));
        $this->assertFalse(Arr::has($array, 'a1.b2'));

        $this->assertTrue(Arr::has($array, 'a1.b1.c1'));
        $this->assertFalse(Arr::has($array, 'a0.b1.c1'));
        $this->assertFalse(Arr::has($array, 'a1.b1.c2'));

        $this->assertTrue(Arr::has($array, 'a2.b2.c2'));
        $this->assertTrue(Arr::has($array, 'a3.b3.c3'));
        $this->assertTrue(Arr::has($array, 'a4.b4.c4'));

        $this->assertTrue(Arr::has($array, ['a1', 'a2.b2.c2', 'a3.b3.c3']));
        $this->assertFalse(Arr::has($array, ['a2.b2.c2', 'a5', 'a3.b3.c3']));
    }

    public function testGet()
    {
        $array = [
            'a1' => [
                'b1' => [
                    'c1' => 0,
                ],
            ],
            'a2' => [
                'b2' => [
                    'c2' => false,
                ],
            ],
            'a3' => [
                'b3' => [
                    'c3' => null,
                ],
            ],
            'a4' => [
                'b4' => [
                    'c4' => ['item'],
                ],
            ],
        ];

        $this->assertSame([
            'b1' => [
                'c1' => 0,
            ],
        ], Arr::get($array, 'a1'));
        $this->assertSame(null, Arr::get($array, 'a0'));
        $this->assertSame(true, Arr::get($array, 'a0', true));

        $this->assertSame([
            'c1' => 0,
        ], Arr::get($array, 'a1.b1'));
        $this->assertSame(null, Arr::get($array, 'a0.b1'));
        $this->assertSame('default', Arr::get($array, 'a1.b2', 'default'));

        $this->assertSame(0, Arr::get($array, 'a1.b1.c1'));
        $this->assertSame(null, Arr::get($array, 'a0.b1.c1'));
        $this->assertSame(1, Arr::get($array, 'a1.b1.c2', 1));

        $this->assertSame(false, Arr::get($array, 'a2.b2.c2'));
        $this->assertSame(null, Arr::get($array, 'a3.b3.c3'));
        $this->assertSame(['item'], Arr::get($array, 'a4.b4.c4'));
    }

    public function testFirst()
    {
        $array = [
            'a1' => [
                'b' => 'c',
            ],
            'a2' => [
                'b' => 'd',
            ],
            'a3' => [
                'b' => 'e',
            ],
        ];

        $this->assertSame([
            'b' => 'c',
        ], Arr::first($array, fn ($item) => 'c' === $item['b']));

        $this->assertSame([
            'b' => 'd',
        ], Arr::first($array, fn ($item) => 'd' === $item['b']));

        $this->assertSame(null, Arr::first($array, fn ($item) => 'f' === $item['b']));
        $this->assertSame('default', Arr::first($array, fn ($item) => 'f' === $item['b'], 'default'));

        $this->assertSame([
            'b' => 'e',
        ], Arr::first($array, fn ($item) => 'e' === Arr::get($item, 'b'), 'default'));
        $this->assertSame('default', Arr::first($array, fn ($item) => 'f' === Arr::get($item, 'c'), 'default'));
    }

    public function testFirstKey()
    {
        $array = [
            'a1' => [
                'b' => 'c',
            ],
            'a2' => [
                'b' => 'd',
            ],
            'a3' => [
                'b' => 'e',
            ],
        ];

        $this->assertSame('a1', Arr::firstKey($array, fn ($item) => 'c' === $item['b']));
        $this->assertSame('a2', Arr::firstKey($array, fn ($item) => 'd' === $item['b']));

        $this->assertSame(null, Arr::firstKey($array, fn ($item) => 'f' === $item['b']));
        $this->assertSame('default', Arr::firstKey($array, fn ($item) => 'f' === $item['b'], 'default'));

        $this->assertSame('a3', Arr::firstKey($array, fn ($item) => 'e' === Arr::get($item, 'b'), 'default'));
        $this->assertSame('default', Arr::firstKey($array, fn ($item) => 'f' === Arr::get($item, 'c'), 'default'));
    }
}
