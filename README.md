# Reflection benchmark

This is a benchmark of all methods allowing to access private properties (read and write).


## Methods

- [`Reflection` API](http://php.net/manual/en/book.reflection.php): read & write

```php
$reflectionProperty = new ReflectionProperty($object, $property);
$reflectionProperty->setAccessible(true);

$value = $reflectionProperty->getValue($object);
$reflectionProperty->setValue($object, 'new value');
```

- [Cast an object to an array](http://ocramius.github.io/blog/fast-php-object-to-array-conversion/): read

```php
$array = (array) $object;

$protectedKey = "\0*\0" . $this->propertyName;
$privateKey = "\0" . get_class($this->object) . "\0" . $this->propertyName;

if (array_key_exists($protectedKey, $array)) {
	$value = $array[$protectedKey];
} elseif (array_key_exists($privateKey, $array)) {
	$value = $array[$privateKey];
}
```

- [Bind a closure to the scope of an object](http://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/): read & write, but on PHP >= 5.4

```php
$get = Closure::bind(function($object, $prop) { return $object->{$prop}; }, null, $object);
$set = Closure::bind(function($object, $prop, $value) { $object->{$prop} = $value; }, null, $object);

$value = $get($object, $property);
$set($object, $property, 'new value');
```


## Running the benchmarks

Install the dependencies using Composer:

    $ composer install

Run the benchmarks:

	$ php vendor/bin/athletic -p benchmarks -b vendor/autoload.php


## Results

- Read a single property:

Casting to an array is faster by not so much.

- Read the same property on many objects:

Reflection is faster than other methods.

- Reading all the properties of an object:

The closure is way faster.

- Writing a single property:

Reflection is slightly faster.

- Writing the same property on many objects:

Reflection is way faster.


### Raw results

```
Benchmark\ReadManyPropertiesEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000021422625] [466,796.21159]
    arrayCast : [10,000    ] [0.0000049477577] [202,111.75575]
    closure   : [10,000    ] [0.0000030014038] [333,177.42755]


Benchmark\ReadPropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000050424337] [198,316.93421]
    arrayCast : [10,000    ] [0.0000043929338] [227,638.30168]
    closure   : [10,000    ] [0.0000049844980] [200,622.00751]


Benchmark\ReadWholeObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000294789314] [33,922.53218]
    arrayCast : [10,000    ] [0.0000150936604] [66,252.98148]
    closure   : [10,000    ] [0.0000066485167] [150,409.49010]


Benchmark\WriteManyPropertiesEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000018604040] [537,517.65324]
    closure   : [10,000    ] [0.0000051559448] [193,950.87304]


Benchmark\WritePropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000052271366] [191,309.32942]
    closure   : [10,000    ] [0.0000056527853] [176,903.94146]
```


## Contribute

Please provide fixes and improvements through pull requests.

Don't forget to update the results in the README.

### TODO

- Add Vagrant config for a standard test environment


## Credits

This is merely a compilation of the methods Marco Pivetta exposed on [his blog](http://ocramius.github.io/).
