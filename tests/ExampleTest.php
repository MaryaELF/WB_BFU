<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        $this->assertTrue(true);
    }
    
    public function testStringContains(): void
    {
        $this->assertStringContainsString('Hello', 'Hello World');
    }
    
    public function testArrayHasKey(): void
    {
        $array = ['name' => 'John', 'age' => 25];
        $this->assertArrayHasKey('name', $array);
    }
}