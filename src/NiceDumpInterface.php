<?php
/**
 * This file is a part of the nicedump package.
 *
 * Read more at https://github.com/themichaelhall/nicedump
 */
declare(strict_types=1);

namespace NiceDump;

/**
 * Interface for NiceDump class.
 *
 * @since 1.0.0
 */
interface NiceDumpInterface
{
    /**
     * Comment parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_COMMENT = 'comment';

    /**
     * Is static parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_IS_STATIC = 'isStatic';

    /**
     * Items parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_ITEMS = 'items';

    /**
     * Key parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_KEY = 'key';

    /**
     * Name parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_NAME = 'name';

    /**
     * Relation parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_REL = 'rel';

    /**
     * Size parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_SIZE = 'size';

    /**
     * Type parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_TYPE = 'type';

    /**
     * Type name parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_TYPE_NAME = 'typeName';

    /**
     * Value parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_VALUE = 'value';

    /**
     * Visibility parameter name.
     *
     * @since 1.0.0
     */
    const PARAMETER_VISIBILITY = 'visibility';

    /**
     * Array type value.
     *
     * @since 1.0.0
     */
    const TYPE_ARRAY = 'array';

    /**
     * Boolean type value.
     *
     * @since 1.0.0
     */
    const TYPE_BOOL = 'bool';

    /**
     * Float type value.
     *
     * @since 1.0.0
     */
    const TYPE_FLOAT = 'float';

    /**
     * Group type value.
     *
     * @since 1.0.0
     */
    const TYPE_GROUP = '_group_';

    /**
     * Integer type value.
     *
     * @since 1.0.0
     */
    const TYPE_INT = 'int';

    /**
     * Null type value.
     *
     * @since 1.0.0
     */
    const TYPE_NULL = 'null';

    /**
     * Object type value.
     *
     * @since 1.0.0
     */
    const TYPE_OBJECT = 'object';

    /**
     * Resource type value.
     *
     * @since 1.0.0
     */
    const TYPE_RESOURCE = 'resource';

    /**
     * String type value.
     *
     * @since 1.0.0
     */
    const TYPE_STRING = 'string';

    /**
     * Text type value.
     *
     * @since 1.0.0
     */
    const TYPE_TEXT = '_text_';

    /**
     * Private visibility value.
     *
     * @since 1.0.0
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * Protected visibility value.
     *
     * @since 1.0.0
     */
    const VISIBILITY_PROTECTED = 'protected';

    /**
     * Public visibility value.
     *
     * @since 1.0.0
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * Returns the NiceDump as a string.
     *
     * @since 1.0.0
     *
     * @return string The NiceDump as a string.
     */
    public function __toString(): string;
}
