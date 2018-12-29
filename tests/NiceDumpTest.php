<?php

declare(strict_types=1);

namespace NiceDump\Tests;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use NiceDump\NiceDump;
use NiceDump\NiceDumpInterface;
use NiceDump\Tests\Helpers\ChildTestClass;
use NiceDump\Tests\Helpers\EmptyTestClass;
use NiceDump\Tests\Helpers\GroupSerializableTestClass;
use NiceDump\Tests\Helpers\Recursive1TestClass;
use NiceDump\Tests\Helpers\Recursive2TestClass;
use NiceDump\Tests\Helpers\SimpleTestClass;
use NiceDump\Tests\Helpers\StringableTestClass;
use NiceDump\Tests\Helpers\TextSerializableTestClass;
use PHPUnit\Framework\TestCase;

/**
 * Test NiceDump class.
 */
class NiceDumpTest extends TestCase
{
    /**
     * Test PARAMETER_* constants.
     */
    public function testParameterConstants()
    {
        self::assertSame('comment', NiceDumpInterface::PARAMETER_COMMENT);
        self::assertSame('isStatic', NiceDumpInterface::PARAMETER_IS_STATIC);
        self::assertSame('items', NiceDumpInterface::PARAMETER_ITEMS);
        self::assertSame('key', NiceDumpInterface::PARAMETER_KEY);
        self::assertSame('name', NiceDumpInterface::PARAMETER_NAME);
        self::assertSame('rel', NiceDumpInterface::PARAMETER_REL);
        self::assertSame('size', NiceDumpInterface::PARAMETER_SIZE);
        self::assertSame('type', NiceDumpInterface::PARAMETER_TYPE);
        self::assertSame('typeName', NiceDumpInterface::PARAMETER_TYPE_NAME);
        self::assertSame('value', NiceDumpInterface::PARAMETER_VALUE);
        self::assertSame('visibility', NiceDumpInterface::PARAMETER_VISIBILITY);
    }

    /**
     * Test TYPE_* constants.
     */
    public function testTypeConstants()
    {
        self::assertSame('array', NiceDumpInterface::TYPE_ARRAY);
        self::assertSame('bool', NiceDumpInterface::TYPE_BOOL);
        self::assertSame('float', NiceDumpInterface::TYPE_FLOAT);
        self::assertSame('_group_', NiceDumpInterface::TYPE_GROUP);
        self::assertSame('int', NiceDumpInterface::TYPE_INT);
        self::assertSame('null', NiceDumpInterface::TYPE_NULL);
        self::assertSame('object', NiceDumpInterface::TYPE_OBJECT);
        self::assertSame('resource', NiceDumpInterface::TYPE_RESOURCE);
        self::assertSame('string', NiceDumpInterface::TYPE_STRING);
        self::assertSame('_text_', NiceDumpInterface::TYPE_TEXT);
    }

    /**
     * Test VISIBILITY_* constants.
     */
    public function testVisibilityConstants()
    {
        self::assertSame('private', NiceDumpInterface::VISIBILITY_PRIVATE);
        self::assertSame('protected', NiceDumpInterface::VISIBILITY_PROTECTED);
        self::assertSame('public', NiceDumpInterface::VISIBILITY_PUBLIC);
    }

    /**
     * Test for scalars (and null).
     *
     * @dataProvider scalarsDataProvider
     *
     * @param mixed  $var          The variable to debug.
     * @param string $expectedJson The expected Json result.
     */
    public function testScalars($var, string $expectedJson)
    {
        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode($expectedJson), 120, PHP_EOL) .
            '=====END NICE-DUMP=====', $niceDump->__toString());
    }

    /**
     * Data provider for testScalars.
     *
     * @return array
     */
    public function scalarsDataProvider()
    {
        return [
            [null, '{"type":"null"}'],
            [false, '{"type":"bool","value":"false"}'],
            [true, '{"type":"bool","value":"true"}'],
            [100, '{"type":"int","value":"100"}'],
            [-42.5, '{"type":"float","value":"-42.5"}'],
            ['Foo Bar Baz', '{"type":"string","value":"Foo Bar Baz","size":11}'],
            ['Å"Ä`\\Ö\'', '{"type":"string","value":"\\u00c5\\"\\u00c4`\\\\\\u00d6\'","size":10}'],
            ['Foo ' . chr(229) . ' Baz', '{"type":"string","value":"Foo \\ufffd Baz","size":11}'],
        ];
    }

    /**
     * Test for an array.
     */
    public function testArray()
    {
        $var = [
            'Foo' => 'Bar',
            1     => [2, 'Baz' => false],
        ];

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"array","size":2,"items":[' .
                '{"key":{"type":"string","value":"Foo","size":3},"value":{"type":"string","value":"Bar","size":3}},' .
                '{"key":{"type":"int","value":"1"},"value":{"type":"array","size":2,"items":[' .
                '{"key":{"type":"int","value":"0"},"value":{"type":"int","value":"2"}},' .
                '{"key":{"type":"string","value":"Baz","size":3},"value":{"type":"bool","value":"false"}}' .
                ']}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test with name parameter.
     */
    public function testWithName()
    {
        $var = 'My value';

        $niceDump = NiceDump::create($var, 'My name');

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"name":"My name","type":"string","value":"My value","size":8}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test with comment parameter.
     */
    public function testWithComment()
    {
        $var = 'My value';

        $niceDump = NiceDump::create($var, 'My name', 'My comment');

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"name":"My name","comment":"My comment","type":"string","value":"My value","size":8}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test for a simple object.
     */
    public function testSimpleObject()
    {
        $var = new SimpleTestClass();

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\SimpleTestClass","items":[' .
                '{"value":{"visibility":"public","name":"publicVar","type":"int","value":"1"}},' .
                '{"value":{"visibility":"protected","name":"protectedVar","type":"string","value":"Foo","size":3}},' .
                '{"value":{"visibility":"private","name":"privateVar","type":"array","size":1,"items":[' .
                '{"key":{"type":"string","value":"Bar","size":3},"value":{"type":"float","value":"0.5"}}' .
                ']}},' .
                '{"value":{"type":"_group_","value":"static properties","items":[' .
                '{"value":{"isStatic":true,"visibility":"public","name":"publicStaticVar","type":"null"}},' .
                '{"value":{"isStatic":true,"visibility":"protected","name":"protectedStaticVar","type":"bool","value":"false"}},' .
                '{"value":{"isStatic":true,"visibility":"private","name":"privateStaticVar","type":"string","value":"Baz","size":3}}' .
                ']}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test for a object that can be represented as a string.
     */
    public function testStringableObject()
    {
        $var = new StringableTestClass('Foo');

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\StringableTestClass","value":"Hello from Foo","items":[' .
                '{"value":{"visibility":"private","name":"label","type":"string","value":"Foo","size":3}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test for an object with parent classes.
     */
    public function testParentClass()
    {
        $var = new ChildTestClass();

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\ChildTestClass","value":"Foo=2","items":[' .
                '{"value":{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\ParentTestClass","rel":"parent","items":[' .
                '{"value":{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\AbstractParentTestClass","rel":"parent","items":[' .
                '{"value":{"visibility":"private","name":"Foo","type":"int","value":"1"}}' .
                ']}},' .
                '{"value":{"visibility":"private","name":"Foo","type":"int","value":"2"}},' .
                '{"value":{"visibility":"public","name":"Bar","type":"int","value":"3"}}' .
                ']}},' .
                '{"value":{"visibility":"public","name":"Baz","type":"int","value":"4"}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test date and time classes.
     */
    public function testDateTimeClasses()
    {
        $var = [
            new DateTime('2000-01-02 03:04:05', new DateTimeZone('+0200')),
            new DateTimeImmutable('2006-07-08 09:10:11', new DateTimeZone('-0400')),
            new DateInterval('P1Y2M3DT4H5M6S'),
        ];

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"array","size":3,"items":[' .
                '{"key":{"type":"int","value":"0"},"value":{"type":"object","typeName":"DateTime","value":"2000-01-02 03:04:05 +0200"}},' .
                '{"key":{"type":"int","value":"1"},"value":{"type":"object","typeName":"DateTimeImmutable","value":"2006-07-08 09:10:11 -0400"}},' .
                '{"key":{"type":"int","value":"2"},"value":{"type":"object","typeName":"DateInterval","value":"1y 2m 3d 4h 5m 6s"}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test recursion.
     */
    public function testRecursion()
    {
        $recursive2 = new Recursive2TestClass();
        $var = new Recursive1TestClass($recursive2);
        $recursive2->recursive1 = $var;

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\Recursive1TestClass","value":"Recursive 1","items":[' .
                '{"value":{"visibility":"private","name":"recursive2","type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\Recursive2TestClass","value":"Recursive 2","items":[' .
                '{"value":{"visibility":"public","name":"recursive1","type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\Recursive1TestClass","value":"Recursive 1","comment":"recursion"}}' .
                ']}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test empty class and array.
     */
    public function testEmpty()
    {
        $var = [
            new EmptyTestClass(),
            [],
        ];

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"array","size":2,"items":[' .
                '{"key":{"type":"int","value":"0"},"value":{"type":"object","typeName":"NiceDump\\\\Tests\\\\Helpers\\\\EmptyTestClass"}},' .
                '{"key":{"type":"int","value":"1"},"value":{"type":"array","size":0}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test resource.
     */
    public function testResource()
    {
        $var = opendir(sys_get_temp_dir());

        $niceDump = NiceDump::create($var);

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"type":"resource","typeName":"stream"}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }

    /**
     * Test NiceDumpSerializable.
     */
    public function testNiceDumpSerializable()
    {
        $var = new GroupSerializableTestClass([
            new TextSerializableTestClass('Foo'),
            new TextSerializableTestClass('Bar'),
        ]);

        $niceDump = NiceDump::create($var, 'Group', 'This is my group');

        self::assertSame(
            '=====BEGIN NICE-DUMP=====' . PHP_EOL .
            chunk_split(base64_encode(
                '{"name":"Group","comment":"This is my group","type":"_group_","value":"Group items","items":[' .
                '{"value":{"type":"_text_","value":"Foo"}},' .
                '{"value":{"type":"_text_","value":"Bar"}}' .
                ']}'
            ), 120, PHP_EOL) .
            '=====END NICE-DUMP=====',
            $niceDump->__toString());
    }
}
