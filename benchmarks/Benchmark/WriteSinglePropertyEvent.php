<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

class WriteSinglePropertyEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    /**
     * @var ReflectionProperty
     */
    private $reflectionProperty;
    private $closure;

    public function setUp()
    {
        $this->object       = new Foo('test');
        $this->propertyName = $prop = 'prop';

        $this->reflectionProperty = new ReflectionProperty(get_class($this->object), $this->propertyName);
        $this->reflectionProperty->setAccessible(true);

        $this->closure = Closure::bind(
            function ($object, $value) use ($prop) {
                $object->$prop = $value;
            },
            $this->object,
            $this->object
        );
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $this->reflectionProperty->setValue($this->object, 'test2');
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        $this->closure->__invoke($this->object, 'test2');
    }
}
