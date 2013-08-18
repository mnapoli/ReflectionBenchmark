<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

class WritePropertyEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    private $closure;

    public function setUp()
    {
        $this->object = new Foo('test');
        $this->propertyName = 'prop';
        $this->closure = function($prop, $value) {
            $this->{$prop} = $value;
        };
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $reflectionProperty = new ReflectionProperty($this->object, $this->propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->object, 'test2');
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        if (!class_exists('Closure')) {
            throw new \Exception("works on PHP 5.4");
        }
        $closure = Closure::bind($this->closure, $this->object, $this->object);
        $closure($this->propertyName, 'test2');
    }
}
