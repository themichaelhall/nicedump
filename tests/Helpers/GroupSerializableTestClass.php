<?php

declare(strict_types=1);

namespace NiceDump\Tests\Helpers;

use NiceDump\NiceDumpInterface;
use NiceDump\NiceDumpSerializable;

/**
 * A test class that serializes as a group.
 */
class GroupSerializableTestClass implements NiceDumpSerializable
{
    /**
     * GroupSerializableTestClass constructor.
     *
     * @param NiceDumpSerializable[] $items The items.
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Returns the serialized content.
     *
     * @return array The serialized content.
     */
    public function niceDumpSerialize(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = [
                NiceDumpInterface::PARAMETER_VALUE => $item->niceDumpSerialize(),
            ];
        }

        return [
            NiceDumpInterface::PARAMETER_TYPE  => NiceDumpInterface::TYPE_GROUP,
            NiceDumpInterface::PARAMETER_VALUE => 'Group items',
            NiceDumpInterface::PARAMETER_ITEMS => $items,
        ];
    }

    /**
     * @var NiceDumpSerializable[] The items.
     */
    private array $items;
}
