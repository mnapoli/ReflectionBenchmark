<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionClass;
use ReflectionProperty;

/**
 * Verifies execution time for instantiation + usage of various reflection-ish techniques
 * used to access all the properties of a class
 */
class ReadWholeObjectEvent extends AthleticEvent
{
    private $object;

    public function setUp()
    {
        $this->object = new Foo('test');
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $class = new ReflectionClass($this->object);
        $data  = array();

        foreach ($class->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);

            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($this->object);
        }

        return $data;
    }

    /**
     * @iterations 10000
     */
    public function arrayCast()
    {
        $data = array();

        foreach ((array) $this->object as $key => $value) {
            if (($nullChar = strrpos($key, "\0")) !== false) {
                $data[substr($key, $nullChar + 1)] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        return Closure::bind(
            function ($object) {
                // @todo this doesn't read parent class' private properties
                return get_object_vars($object);
            },
            null,
            $this->object
        )->__invoke($this->object);
    }
}
