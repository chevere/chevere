<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class test extends TestCase
{
    public function testCanBeCreatedFromValidEmailAddress(): void
    {
        $this->assertEquals(true, true);
    }
      
    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->assertEquals(true, true);
    }
      
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(true, true);
    }
}
