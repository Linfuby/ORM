<?php

namespace PHPixieTests\ORM\Relationships\Relationship;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Relationship\Property
 */
abstract class PropertyTest extends \PHPixieTests\AbstractORMTest
{
    protected $property;
    protected $handler;
    protected $side;

    public function setUp()
    {
        $this->handler  = $this->handler();
        $this->side     = $this->side();
        $this->property = $this->property();
    }

    /**
     * @covers ::construct
     * @covers \PHPixie\ORM\Relationships\Relationship\Property::construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {

    }


    protected abstract function property();
    protected abstract function handler();
    protected abstract function side();
}
