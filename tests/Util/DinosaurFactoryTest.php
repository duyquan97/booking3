<?php
//
//namespace Tests\AppBundle\Factory;
//
//use App\Entity\Dinosaur;
//use PHPUnit\Framework\TestCase;
//use App\Factory\DinosaurFactory;
//
//class DinosaurFactoryTest extends TestCase
//{
//    private $factory;
//
//    public function setUp()
//    {
//        //If you have a method that's exactly called setUp, PHPUnit will automatically call it before each test.
//        $this->factory = new DinosaurFactory();
//    }
//    public function testItGrowsALargeVelociraptor()
//    {
//        $dinosaur = $this->factory->growVelociraptor(5);
//
//        $this->assertInstanceOf(Dinosaur::class, $dinosaur);
//        $this->assertInternalType('string', $dinosaur->getGenus());
//        $this->assertSame('Velociraptor', $dinosaur->getGenus());
//        $this->assertSame(5, $dinosaur->getLength());
//    }
//
//    public function testItGrowsATriceraptors()
//    {
//        //Mark the test as incomplete.
//        $this->markTestIncomplete('Waiting for confirmation from GenLab');
//    }
//
//    public function testItGrowsABabyVelociraptor()
//    {
//        if (!class_exists('Nancy')) {
//            //Mark the test as skipped
//            $this->markTestSkipped('There is nobody to watch the baby!');
//        }
//        $dinosaur = $this->factory->growVelociraptor(1);
//        $this->assertSame(1, $dinosaur->getLength());
//    }
//
//    public function testItGrowsADinosaurFromSpecification()
//    {
//
//    }
//}
