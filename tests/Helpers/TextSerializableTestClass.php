<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

use NiceDump\NiceDumpInterface;
use NiceDump\NiceDumpSerializable;

/**
 * A test class that serializes as a plain text.
 */
class TextSerializableTestClass implements NiceDumpSerializable
{
    /**
     * TextSerializableTestClass constructor.
     *
     * @param string $text The text.
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * Returns the serialized content.
     *
     * @return array The serialized content.
     */
    public function niceDumpSerialize(): array
    {
        return [
            NiceDumpInterface::PARAMETER_TYPE  => NiceDumpInterface::TYPE_TEXT,
            NiceDumpInterface::PARAMETER_VALUE => $this->text,
        ];
    }

    /**
     * @var string
     */
    private string $text;
}
