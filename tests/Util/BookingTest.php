<?php
// tests/Util/CalculatorTest.php
namespace App\Tests\Util;

use App\Entity\Booking;
use App\Entity\Guests;
use App\Util\Calculator;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    public function testSettingLength()
    {
        $booking = new Booking();
        $this->assertEquals(0, $booking->getAccept());

        $booking->setAccept(1);
        $this->assertSame(1, $booking->getAccept());
    }

    public function testBookingHasNot()
    {
        $booking = new Booking();
        $booking->setAccept(15);

        $this->assertGreaterThan(12, $booking->getAccept(), 'hehe Di duong quyen khong');
    }
}
