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
    public $publicVar;

    /**
     * @var string
     */
    protected $protectedVar;

    /**
     * @var array
     */
    private $privateVar;

    /**
     * @var null
     */
    public static $publicStaticVar;

    /**
     * @var bool
     */
    protected static $protectedStaticVar;

    /**
     * @var string
     */
    private static $privateStaticVar;
}
