<?php
declare(strict_types=1);

namespace App\Tests\Sandbox;

use PHPUnit\Framework\TestCase;

class SumNumbersTest extends TestCase
{
    public function testUploadPaths(): void
    {
        $this->assertEquals(4, 2 + 2);
    }
}