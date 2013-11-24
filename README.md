# Reflection benchmark

This is a benchmark of all methods allowing to access private properties (read and write).

We all know that micro-optimization is most of the time useless. However, if you write libraries or frameworks
making extensive use of reflection techniques, you probably wonder how to spare the impact of using reflection
to your users. This comparison is here to help by maintaining an up-to-date and reproducible summary.


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

```sh
$ composer install
```

Run the benchmarks:

```sh
$ php -n vendor/bin/athletic -p benchmarks -b vendor/autoload.php
```

## Results

Those results were obtained on a development OS X machine using PHP 5.5.5. Feel free to run the benchmark
on your server and update these results if you get different ones. Be aware that "slightly faster" means that
it's probably not significant, whereas "faster" or "way faster" means that results are expected to be
reproducible for the same PHP version.

- Read a single property on *one* object:

The closure is slightly faster.

- Read a single property on *many* objects:

Reflection is way faster.

- Reading all the properties of an object:

The closure is faster.

- Writing a single property on *one* object:

Reflection is slightly faster.

- Writing a single property on *many* objects:

Reflection is way faster.


### Raw results

```
Benchmark\ReadAllPropertiesOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000097395658] [102,673.98111]
    arrayCast : [10,000    ] [0.0000049465656] [202,160.46348]
    closure   : [10,000    ] [0.0000042748213] [233,927.90813]


Benchmark\ReadSinglePropertyOnManyObjectsEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [100,000   ] [0.0000005387926] [1,856,001.69922]
    arrayCast : [100,000   ] [0.0000017712998] [564,557.15626]
    closure   : [100,000   ] [0.0000009075737] [1,101,838.89204]


Benchmark\ReadSinglePropertyOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [100,000   ] [0.0000018299770] [546,454.94484]
    arrayCast : [100,000   ] [0.0000022302055] [448,389.16589]
    closure   : [100,000   ] [0.0000015761971] [634,438.40237]


Benchmark\WriteSinglePropertyOnManyObjectsEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [50,000    ] [0.0000006345797] [1,575,846.28910]
    closure   : [50,000    ] [0.0000011913967] [839,350.98097]


Benchmark\WriteSinglePropertyOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [50,000    ] [0.0000018200827] [549,425.59451]
    closure   : [50,000    ] [0.0000019398355] [515,507.61652]
```


## Contribute

Please provide fixes and improvements through pull requests.

Don't forget to update the results in the README.

### TODO

- Add Vagrant config for a standard test environment


## Credits

This is a compilation of the methods Marco Pivetta exposed on [his blog](http://ocramius.github.io/).
