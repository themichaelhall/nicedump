<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * A child test class.
 */
class ChildTestClass extends ParentTestClass
{
    /**
     * ChildTestClass constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->Baz = 4;
    }

    /**
     * @var int
     */
    public int $Baz;
}
