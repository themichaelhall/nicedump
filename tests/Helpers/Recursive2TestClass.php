<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * A test class that contains a recursive reference.
 */
class Recursive2TestClass
{
    /**
     * Recursive2TestClass constructor.
     */
    public function __construct()
    {
        $this->recursive1 = null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'Recursive 2';
    }

    /**
     * @var null|Recursive1TestClass
     */
    public ?Recursive1TestClass $recursive1;
}
