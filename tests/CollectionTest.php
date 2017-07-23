<?php

namespace Malios\Sortaviz\Test;

use Malios\Sortaviz\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testBasicOperations()
    {
        $coll = new Collection(...[2, 1, 3]);
        $this->assertEquals(3, $coll->count());
        $this->assertTrue($coll->lessThan(0, 2));
        $this->assertFalse($coll->lessThan(0, 1));
        $coll->swap(0, 1);
        $this->assertSame([1.0, 2.0, 3.0], $coll->toArray());
    }
}
