<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

/**
 * Verifies execution time for usage of various reflection-ish techniques
 * used to access properties
 */
class ReadSinglePropertyOnManyObjectsEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    /**
     * @var ReflectionProperty
     */
    private $reflectionProperty;
    private $closure;
    private $protectedKey;
    private $privateKey;

    public function setUp()
    {
        $this->object             = new Foo('test');
        $this->propertyName       = $prop = 'prop';
        $this->reflectionProperty = new ReflectionProperty(get_class($this->object), $this->propertyName);

        $this->reflectionProperty->setAccessible(true);

        $this->closure = Closure::bind(
            function ($object) use ($prop) {
                return $object->{$prop};
            },
            null,
            get_class($this->object)
        );
        $this->protectedKey = "\0*\0" . $this->propertyName;
        $this->privateKey   = "\0" . get_class($this->object) . "\0" . $this->propertyName;
    }

    /**
     * @iterations 10000
     * @baseLine
     */
    public function reflection()
    {
        return $this->reflectionProperty->getValue($this->object);
    }

    /**
     * @iterations 10000
     */
    public function arrayCast()
    {
        $array = (array) $this->object;

        if (array_key_exists($this->protectedKey, $array)) {
            return $array[$this->protectedKey];
        } elseif (array_key_exists($this->privateKey, $array)) {
            return $array[$this->privateKey];
        }

        throw new \Exception("property doesn't exist");
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        return $this->closure->__invoke($this->object);
    }
}
