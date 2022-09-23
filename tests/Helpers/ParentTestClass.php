<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * A parent test class.
 */
class ParentTestClass extends AbstractParentTestClass
{
    /**
     * ParentTestClass constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->Foo = 2;
        $this->Bar = 3;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'Foo=' . $this->Foo;
    }

    /**
     * @var int
     */
    private int $Foo;

    /**
     * @var int
     */
    public int $Bar;
}
