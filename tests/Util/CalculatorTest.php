<?php
// tests/Util/CalculatorTest.php
namespace App\Tests\Util;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testReturnsFullSpecificationOfDinosaur()
    {
        $dinosaur = new Dinosaur('Tyrannosaurus', true);
        $dinosaur->setLength(12);
        $this->assertSame(
            'The Tyrannosaurus  carnivorous dinosaur is 12 meters long',
            $dinosaur->getSpecification()
        );
    }
}
