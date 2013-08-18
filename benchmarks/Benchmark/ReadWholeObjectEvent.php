<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionClass;
use ReflectionProperty;

class ReadWholeObjectEvent extends AthleticEvent
{
    private $object;
    private $closure;

    public function setUp()
    {
        $this->object = new Foo('test');
        $this->closure = function($object) {
            return get_object_vars($object);
        };
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $class = new ReflectionClass($this->object);
        $data = array();
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
        $classname = get_class($this->object);
        foreach ((array) $this->object as $key => $value) {
            if (strpos("\0*\0", $key) !== false) {
                $data[substr($key, 3, strlen($key) - 3)] = $value;
            } elseif (strpos("\0$classname\0", $key) !== false) {
                $data[substr($key, strlen("\0$classname\0"), strlen($key) - strlen("\0$classname\0"))] = $value;
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
        if (!class_exists('Closure')) {
            throw new \Exception("works on PHP 5.4");
        }
        $closure = Closure::bind($this->closure, null, $this->object);
        return $closure($this->object);
    }
}
