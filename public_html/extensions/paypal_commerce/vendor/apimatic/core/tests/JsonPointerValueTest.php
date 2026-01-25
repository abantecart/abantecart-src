<?php

namespace Core\Tests;

use PHPUnit\Framework\TestCase;
use Core\Utils\JsonPointerValue;

class JsonPointerValueTest extends TestCase
{
    private $testData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testData = json_encode([
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'john@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'country' => 'USA'
            ],
            'tags' => ['developer', 'php', 'testing'],
            'active' => true,
            'score' => 95.5,
            'metadata' => null
        ]);
    }

    /**
     * Test getting a simple string value
     */
    public function testGetSimpleStringValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/name');

        $this->assertEquals('John Doe', $result);
    }

    /**
     * Test getting an integer value
     */
    public function testGetIntegerValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/age');

        $this->assertEquals(30, $result);
    }

    /**
     * Test getting a boolean value
     */
    public function testGetBooleanValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/active');

        $this->assertTrue($result);
    }

    /**
     * Test getting a float value
     */
    public function testGetFloatValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/score');

        $this->assertEquals(95.5, $result);
    }

    /**
     * Test getting a null value
     */
    public function testGetNullValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/metadata');

        $this->assertNull($result);
    }

    /**
     * Test getting a nested value
     */
    public function testGetNestedValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/address/city');

        $this->assertEquals('New York', $result);
    }

    /**
     * Test getting an array value
     */
    public function testGetArrayValue()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/tags');

        $this->assertIsArray($result);
        $this->assertEquals(['developer', 'php', 'testing'], $result);
    }

    /**
     * Test getting a specific array element
     */
    public function testGetArrayElement()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/tags/0');

        $this->assertEquals('developer', $result);
    }

    /**
     * Test with empty pointer string
     */
    public function testEmptyPointerString()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '');

        $this->assertEquals('', $result);
    }

    /**
     * Test with whitespace-only pointer string
     */
    public function testWhitespaceOnlyPointerString()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '   ');

        $this->assertEquals('', $result);
    }

    /**
     * Test with empty json string
     */
    public function testEmptyJsonString()
    {
        $result = JsonPointerValue::getJsonPointerValue('', '/any');

        $this->assertEquals('', $result);
    }

    /**
     * Test with whitespace-only json string
     */
    public function testWhitespaceOnlyJsonString()
    {
        $result = JsonPointerValue::getJsonPointerValue('   ', '/any');

        $this->assertEquals('', $result);
    }

    /**
     * Test with both empty json and empty pointer
     */
    public function testEmptyJsonAndEmptyPointer()
    {
        $result = JsonPointerValue::getJsonPointerValue('', '');

        $this->assertEquals('', $result);
    }

    /**
     * Test with invalid pointer that doesn't exist
     */
    public function testInvalidPointerPath()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/nonexistent');

        $this->assertEquals('', $result);
    }

    /**
     * Test with invalid nested pointer
     */
    public function testInvalidNestedPointerPath()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/address/zipcode');

        $this->assertEquals('', $result);
    }

    /**
     * Test with malformed pointer (missing leading slash)
     */
    public function testMalformedPointer()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, 'name');

        $this->assertEquals('', $result);
    }

    /**
     * Test getting an object value (should be serialized)
     */
    public function testGetObjectValueSerialization()
    {
        $objectData = json_encode([
            'user' => (object)[
                'name' => 'Jane',
                'role' => 'admin'
            ]
        ]);

        $result = JsonPointerValue::getJsonPointerValue($objectData, '/user');
        $this->assertNotEmpty($result);
    }

    /**
     * Test with array index out of bounds
     */
    public function testArrayIndexOutOfBounds()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/tags/999');

        $this->assertEquals('', $result);
    }

    /**
     * Test with negative array index
     */
    public function testNegativeArrayIndex()
    {
        $result = JsonPointerValue::getJsonPointerValue($this->testData, '/tags/-1');

        $this->assertEquals('', $result);
    }

    /**
     * Test with deeply nested valid pointer
     */
    public function testDeeplyNestedPointer()
    {
        $deepData = json_encode([
            'level1' => [
                'level2' => [
                    'level3' => [
                        'value' => 'deep value'
                    ]
                ]
            ]
        ]);

        $result = JsonPointerValue::getJsonPointerValue($deepData, '/level1/level2/level3/value');

        $this->assertEquals('deep value', $result);
    }

    /**
     * Test with special characters in pointer path
     */
    public function testSpecialCharactersInPath()
    {
        $specialData = json_encode([
            'key~with~tildes' => 'value1',
            'key/with/slashes' => 'value2'
        ]);

        $result = JsonPointerValue::getJsonPointerValue($specialData, '/key~0with~0tildes');

        $this->assertEquals('value1', $result);
    }

    /**
     * Test with zero value
     */
    public function testZeroValue()
    {
        $zeroData = json_encode(['count' => 0]);

        $result = JsonPointerValue::getJsonPointerValue($zeroData, '/count');

        $this->assertEquals(0, $result);
    }

    /**
     * Test with empty string value
     */
    public function testEmptyStringValue()
    {
        $emptyData = json_encode(['description' => '']);
        $result = JsonPointerValue::getJsonPointerValue($emptyData, '/description');

        $this->assertEquals('', $result);
    }

    /**
     * Test with false boolean value
     */
    public function testFalseBooleanValue()
    {
        $falseData = json_encode(['enabled' => false]);
        $result = JsonPointerValue::getJsonPointerValue($falseData, '/enabled');

        $this->assertFalse($result);
    }

    /**
     * Test that getJsonPointerValue returns empty string when Pointer throws (invalid JSON)
     */
    public function testReturnsEmptyStringOnPointerException()
    {
        $invalidJson = '{invalid json';
        $result = JsonPointerValue::getJsonPointerValue($invalidJson, '/any');
        $this->assertSame('', $result);
    }
}
