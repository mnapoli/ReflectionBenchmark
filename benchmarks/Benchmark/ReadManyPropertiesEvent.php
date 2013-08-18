<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

class ReadManyPropertiesEvent extends AthleticEvent
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
        $this->object = new Foo('test');
        $this->propertyName = 'prop';

        $this->reflectionProperty = new ReflectionProperty($this->object, $this->propertyName);
        $this->reflectionProperty->setAccessible(true);

        $this->closure = function($prop) {
            return $this->{$prop};
        };
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $value = $this->reflectionProperty->getValue($this->object);
    }

    /**
     * @iterations 10000
     */
    public function arrayCast()
    {
        $array = (array) $this->object;
        $protectedKey = "\0*\0" . $this->propertyName;
        $privateKey = "\0" . get_class($this->object) . "\0" . $this->propertyName;
        if (array_key_exists($protectedKey, $array)) {
            return $array[$protectedKey];
        } elseif (array_key_exists($privateKey, $array)) {
            return $array[$privateKey];
        }
        throw new \Exception("property doesn't exist");
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
