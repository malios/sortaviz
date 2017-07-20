<?php

namespace Malios\Sortavis\Test;

use Malios\Sortavis\Collection;
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

    public function testEvents()
    {
        $coll = new Collection(...[1, 2, 3]);

        $postSwapCalled = false;
        $coll->listen('pre.swap', function ($data) use (&$postSwapCalled) {
            $postSwapCalled = true;
        });

        $preSwapCalled = false;
        $coll->listen('post.swap', function ($data) use (&$preSwapCalled) {
            $preSwapCalled = true;
        });
        $coll->swap(1, 2);
        $this->assertTrue($preSwapCalled);
        $this->assertTrue($postSwapCalled);

        $checkLTCalled = false;
        $coll->listen('check.lt', function ($data) use (&$checkLTCalled) {
            $checkLTCalled = true;
        });
        $coll->lessThan(1, 2);

        $this->assertTrue($checkLTCalled);
    }
}
