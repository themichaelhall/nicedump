<?php

declare(strict_types=1);

namespace NiceDump\Tests;

use NiceDump\NiceDump;
use PHPUnit\Framework\TestCase;

/**
 * Test NiceDump global functions.
 */
class FunctionsTest extends TestCase
{
    /**
     * Test nice_dump() function.
     */
    public function testNiceDump()
    {
        $var = 'Foo Bar';

        ob_start();

        nice_dump($var, 'Name', 'Comment');

        $output = ob_get_contents();
        ob_end_clean();

        self::assertSame(NiceDump::create($var, 'Name', 'Comment') . PHP_EOL, $output);
    }

    /**
     * Test nice_dump_html() function.
     */
    public function testNiceDumpHtml()
    {
        $var = 'Foo Bar';

        ob_start();

        nice_dump_html($var, 'Name', 'Comment');

        $output = ob_get_contents();
        ob_end_clean();

        self::assertSame('<!--' . PHP_EOL . NiceDump::create($var, 'Name', 'Comment') . PHP_EOL . '-->' . PHP_EOL, $output);
    }
}
