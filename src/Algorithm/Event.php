<?php declare(strict_types=1);

namespace Malios\Sortaviz\Algorithm;

abstract class Event
{
    const CHECK_LESS_THAN = 'check.lt';
    const FINISH = 'finish';
    const PRE_SWAP = 'pre.swap';
    const POST_SWAP = 'post.swap';
    const SELECT_INDEX = 'select.index';
    const DESELECT_INDEX = 'deselect.index';
}
