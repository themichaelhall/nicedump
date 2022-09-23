<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

/**
 * A simple test class.
 */
class SimpleTestClass
{
    /**
     * SimpleTestClass constructor.
     */
    public function __construct()
    {
        $this->publicVar = 1;
        $this->protectedVar = 'Foo';
        $this->privateVar = ['Bar' => 0.5];

        self::$publicStaticVar = null;
        self::$protectedStaticVar = false;
        self::$privateStaticVar = 'Baz';
    }

    /**
     * @var int
     */
    public int $publicVar;

    /**
     * @var string
     */
    protected string $protectedVar;

    /**
     * @var array
     */
    private array $privateVar;

    /**
     * @var null|object
     */
    public static ?object $publicStaticVar;

    /**
     * @var bool
     */
    protected static bool $protectedStaticVar;

    /**
     * @var string
     */
    private static string $privateStaticVar;
}
