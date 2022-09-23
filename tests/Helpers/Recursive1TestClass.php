<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * A test class that contains a recursive reference.
 */
class Recursive1TestClass
{
    /**
     * Recursive1TestClass constructor.
     *
     * @param Recursive2TestClass $recursive2
     */
    public function __construct(Recursive2TestClass $recursive2)
    {
        $this->recursive2 = $recursive2;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'Recursive 1';
    }

    /**
     * @var Recursive2TestClass
     */
    private Recursive2TestClass $recursive2;
}
