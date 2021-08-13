<?php

/**
 * This file is a part of the nicedump package.
 *
 * Read more at https://github.com/themichaelhall/nicedump
 */

declare(strict_types=1);

use NiceDump\NiceDump;

/**
 * Outputs a variable as a NiceDump.
 *
 * @since 1.0.0
 *
 * @param mixed  $var     The variable.
 * @param string $name    The variable name (optional).
 * @param string $comment The variable comment (optional).
 */
function nice_dump($var, string $name = '', string $comment = ''): void
{
    echo NiceDump::create($var, $name, $comment) . PHP_EOL;
}

/**
 * Outputs a variable as a NiceDump enclosed in HTML-comment tags.
 *
 * @since 1.0.0
 *
 * @param mixed  $var     The variable.
 * @param string $name    The variable name (optional).
 * @param string $comment The variable comment (optional).
 */
function nice_dump_html($var, string $name = '', string $comment = ''): void
{
    echo '<!--' . PHP_EOL . NiceDump::create($var, $name, $comment) . PHP_EOL . '-->' . PHP_EOL;
}
