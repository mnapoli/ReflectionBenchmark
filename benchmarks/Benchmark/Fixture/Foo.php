<?php

namespace Benchmark\Fixture;

class Foo
{
    private $prop;
    protected $prop2;
    public $prop3;
    private $prop4;
    private $prop5;

    public function __construct($prop)
    {
        $this->prop = $prop;
    }
}
