<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

/**
 * Verifies instantiation + execution time for usage of various reflection-ish techniques
 * used to write properties
 */
class WritePropertyEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    private $closure;

    public function setUp()
    {
        $this->object = new Foo('test');
        $this->propertyName = 'prop';
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
        Closure::bind(
            function ($object, $prop, $value) {
                $object->$prop = $value;
            },
            $this->closure,
            $this->object,
            $this->object
        )->__invoke($this->object, $this->propertyName, 'test2');
    }
}
