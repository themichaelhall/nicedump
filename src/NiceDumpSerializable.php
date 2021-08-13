<?php

/**
 * This file is a part of the nicedump package.
 *
 * Read more at https://github.com/themichaelhall/nicedump
 */

declare(strict_types=1);

namespace NiceDump;

/**
 * Interface for custom serializing of a NiceDump.
 *
 * @since 1.0.0
 */
interface NiceDumpSerializable
{
    /**
     * Returns the serialized content.
     *
     * @since 1.0.0
     *
     * @return array The serialized content.
     */
    public function niceDumpSerialize(): array;
}
