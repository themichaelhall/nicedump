<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * An abstract parent test class.
 */
abstract class AbstractParentTestClass
{
    /**
     * AbstractParentTestClass constructor.
     */
    public function __construct()
    {
        $this->Foo = 1;
    }

    /**
     * @var int
     */
    private int $Foo;
}
