<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

class ReadPropertyEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    private $closure;

    public function setUp()
    {
        $this->object = new Foo('test');
        $this->propertyName = 'prop';
        $this->closure = function($prop) {
            return $this->{$prop};
        };
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $reflectionProperty = new ReflectionProperty($this->object, $this->propertyName);
        $reflectionProperty->setAccessible(true);
        $value = $reflectionProperty->getValue($this->object);
    }

    /**
     * @iterations 10000
     */
    public function arrayCast()
    {
        $array = (array) $this->object;
        $value = $array["\0" . get_class($this->object) . "\0" . $this->propertyName];
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        $closure = Closure::bind($this->closure, $this->object, $this->object);
        $value = $closure($this->propertyName);
    }
}
