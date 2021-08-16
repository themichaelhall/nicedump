<?php

/**
 * This file is a part of the nicedump package.
 *
 * Read more at https://github.com/themichaelhall/nicedump
 */

declare(strict_types=1);

namespace NiceDump;

use DateInterval;
use DateTimeInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * NiceDump class.
 *
 * @since 1.0.0
 */
class NiceDump implements NiceDumpInterface
{
    /**
     * Creates a NiceDump from a variable.
     *
     * @since 1.0.0
     *
     * @param mixed  $var     The variable.
     * @param string $name    The variable name (optional).
     * @param string $comment The variable comment (optional).
     *
     * @return NiceDumpInterface The created NiceDump.
     */
    public static function create($var, string $name = '', string $comment = ''): NiceDumpInterface
    {
        $result = new self();

        $oldSubstituteCharacter = mb_substitute_character();
        mb_substitute_character(0xfffd);

        $result->content = $result::buildContent([], $var, [], $name, $comment);

        mb_substitute_character($oldSubstituteCharacter);

        return $result;
    }

    /**
     * Returns the NiceDump as a string.
     *
     * @since 1.0.0
     *
     * @return string The NiceDump as a string.
     */
    public function __toString(): string
    {
        return
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(json_encode($this->content)), 120, PHP_EOL) .
            '=====END NICE-DUMP=====';
    }

    /**
     * Builds content from a variable.
     *
     * @param array    $content         The original content.
     * @param mixed    $var             The variable.
     * @param object[] $previousObjects The objects that have been previously processed.
     * @param string   $name            The variable name (optional).
     * @param string   $comment         The variable comment (optional).
     *
     * @return array The content.
     */
    private static function buildContent(array $content, $var, array $previousObjects, string $name = '', string $comment = ''): array
    {
        if ($name !== '') {
            $content[self::PARAMETER_NAME] = self::fixString($name);
        }

        if ($comment !== '') {
            $content[self::PARAMETER_COMMENT] = self::fixString($comment);
        }

        if (is_bool($var)) {
            $content[self::PARAMETER_TYPE] = self::TYPE_BOOL;
            $content[self::PARAMETER_VALUE] = ($var ? 'true' : 'false');

            return $content;
        }

        if (is_int($var)) {
            $content[self::PARAMETER_TYPE] = self::TYPE_INT;
            $content[self::PARAMETER_VALUE] = strval($var);

            return $content;
        }

        if (is_float($var)) {
            $content[self::PARAMETER_TYPE] = self::TYPE_FLOAT;
            $content[self::PARAMETER_VALUE] = strval($var);

            return $content;
        }

        if (is_string($var)) {
            $var = self::fixString($var);

            $content[self::PARAMETER_TYPE] = self::TYPE_STRING;
            $content[self::PARAMETER_VALUE] = $var;
            $content[self::PARAMETER_SIZE] = strlen($var);

            return $content;
        }

        if (is_array($var)) {
            return self::buildArrayContent($content, $var, $previousObjects);
        }

        if (is_object($var)) {
            $reflectionClass = new ReflectionClass($var);

            return self::buildObjectContent($content, $var, $reflectionClass, $previousObjects, false);
        }

        if (is_resource($var)) {
            $content[self::PARAMETER_TYPE] = self::TYPE_RESOURCE;
            $content[self::PARAMETER_TYPE_NAME] = get_resource_type($var);

            return $content;
        }

        $content[self::PARAMETER_TYPE] = self::TYPE_NULL;

        return $content;
    }

    /**
     * Constructs an empty NiceDump.
     */
    private function __construct()
    {
        $this->content = [];
    }

    /**
     * Build content for an array.
     *
     * @param array    $content         The original content.
     * @param array    $var             The array.
     * @param object[] $previousObjects The objects that have been previously processed.
     *
     * @return array The content.
     */
    private static function buildArrayContent(array $content, array $var, array $previousObjects): array
    {
        $count = count($var);
        $content[self::PARAMETER_TYPE] = self::TYPE_ARRAY;
        $content[self::PARAMETER_SIZE] = $count;

        if ($count === 0) {
            return $content;
        }

        $items = [];
        foreach ($var as $key => $value) {
            $items[] = [
                self::PARAMETER_KEY   => self::buildContent([], $key, $previousObjects),
                self::PARAMETER_VALUE => self::buildContent([], $value, $previousObjects),
            ];
        }

        $content[self::PARAMETER_ITEMS] = $items;

        return $content;
    }

    /**
     * Build content for an object.
     *
     * @param array           $content         The original content.
     * @param object          $var             The object.
     * @param ReflectionClass $reflectionClass The reflection class to use for the object.
     * @param object[]        $previousObjects The objects that have been previously processed.
     * @param bool            $isParentClass   True if this is a parent class, false otherwise.
     *
     * @return array The content.
     */
    private static function buildObjectContent(array $content, object $var, ReflectionClass $reflectionClass, array $previousObjects, bool $isParentClass): array
    {
        if ($var instanceof NiceDumpSerializable) {
            return array_merge($content, $var->niceDumpSerialize());
        }

        $content[self::PARAMETER_TYPE] = self::TYPE_OBJECT;
        $content[self::PARAMETER_TYPE_NAME] = $reflectionClass->getName();

        if ($isParentClass) {
            $content[self::PARAMETER_REL] = 'parent';
        }

        $stringValue = self::getStringValue($var);
        if ($stringValue !== null && !$isParentClass) {
            $content[self::PARAMETER_VALUE] = self::fixString($stringValue);
        }

        // Handle recursion.
        foreach ($previousObjects as $previousObject) {
            if ($previousObject === $var) {
                $content[self::PARAMETER_COMMENT] = 'recursion';

                return $content;
            }
        }

        $items = [];

        // Handle parent class.
        $parentClass = $reflectionClass->getParentClass();
        if ($parentClass !== false) {
            $items[] = [
                self::PARAMETER_VALUE => self::buildObjectContent([], $var, $parentClass, $previousObjects, true),
            ];
        }

        $previousObjects[] = $var;

        // Handle class properties and split them into non-static and static.
        $staticReflectionProperties = [];
        $nonStaticReflectionProperties = [];

        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);

            if ($reflectionProperty->getDeclaringClass()->getName() !== $reflectionClass->getName()) {
                continue;
            }

            if ($reflectionProperty->isStatic()) {
                $staticReflectionProperties[] = $reflectionProperty;
            } else {
                $nonStaticReflectionProperties[] = $reflectionProperty;
            }
        }

        // Non-static properties.
        if (count($nonStaticReflectionProperties) > 0) {
            $items = array_merge($items, self::buildPropertiesContent($var, $nonStaticReflectionProperties, $previousObjects));
        }

        // Static properties as a group.
        if (count($staticReflectionProperties) > 0) {
            $staticPropertiesContent = self::buildPropertiesContent($var, $staticReflectionProperties, $previousObjects);
            $items[] = [
                self::PARAMETER_VALUE => self::buildGroupContent($staticPropertiesContent, 'static properties'),
            ];
        }

        if (count($items) > 0) {
            $content[self::PARAMETER_ITEMS] = $items;
        }

        return $content;
    }

    /**
     * Build content for class properties.
     *
     * @param object               $var                  The object.
     * @param ReflectionProperty[] $reflectionProperties The reflection properties to use for the object.
     * @param object[]             $previousObjects      The objects that have been previously processed.
     *
     * @return array The content.
     */
    private static function buildPropertiesContent(object $var, array $reflectionProperties, array $previousObjects): array
    {
        $items = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $items[] = [
                self::PARAMETER_VALUE => self::buildPropertyContent($var, $reflectionProperty, $previousObjects),
            ];
        }

        return $items;
    }

    /**
     * Build content for a class property.
     *
     * @param object             $var                The object.
     * @param ReflectionProperty $reflectionProperty The reflection property to use for the object.
     * @param object[]           $previousObjects    The objects that have been previously processed.
     *
     * @return array The content.
     */
    private static function buildPropertyContent(object $var, ReflectionProperty $reflectionProperty, array $previousObjects): array
    {
        $content = [];

        if ($reflectionProperty->isStatic()) {
            $content[self::PARAMETER_IS_STATIC] = true;
        }

        if ($reflectionProperty->isPublic()) {
            $content[self::PARAMETER_VISIBILITY] = self::VISIBILITY_PUBLIC;
        } elseif ($reflectionProperty->isProtected()) {
            $content[self::PARAMETER_VISIBILITY] = self::VISIBILITY_PROTECTED;
        } elseif ($reflectionProperty->isPrivate()) {
            $content[self::PARAMETER_VISIBILITY] = self::VISIBILITY_PRIVATE;
        }

        return self::buildContent(
            $content,
            $reflectionProperty->getValue($var),
            $previousObjects,
            $reflectionProperty->getName()
        );
    }

    /**
     * Build content for a group.
     *
     * @param array  $content   The content to build a group for.
     * @param string $groupName The group name.
     *
     * @return array The content.
     */
    private static function buildGroupContent(array $content, string $groupName): array
    {
        $content = [
            self::PARAMETER_TYPE  => self::TYPE_GROUP,
            self::PARAMETER_VALUE => $groupName,
            self::PARAMETER_ITEMS => $content,
        ];

        return $content;
    }

    /**
     * Check and fix an invalid string.
     *
     * @param string $s The string.
     *
     * @return string The result.
     */
    private static function fixString(string $s): string
    {
        return mb_convert_encoding($s, 'UTF-8', 'UTF-8');
    }

    /**
     * Returns the string value for an object.
     *
     * @param object $var The object.
     *
     * @return string|null The string value for the object or null if no string value exists.
     */
    private static function getStringValue(object $var): ?string
    {
        if (method_exists($var, '__toString')) {
            return $var->__toString();
        }

        if ($var instanceof DateTimeInterface) {
            return $var->format('Y-m-d H:i:s O');
        }

        if ($var instanceof DateInterval) {
            return $var->format('%yy %mm %dd %hh %im %ss');
        }

        return null;
    }

    /**
     * @var array My content.
     */
    private $content;
}
