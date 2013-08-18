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

```sh
$ composer install
```

Run the benchmarks:

```sh
$ php -n vendor/bin/athletic -p benchmarks -b vendor/autoload.php
```

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
Benchmark\ReadPropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000019646883] [508,986.59062]
    arrayCast : [10,000    ] [0.0000019546270] [511,606.55258]
    closure   : [10,000    ] [0.0000017421722] [573,996.05868]


Benchmark\ReadSinglePropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000004869938] [2,053,414.27592]
    arrayCast : [10,000    ] [0.0000011763334] [850,099.11024]
    closure   : [10,000    ] [0.0000008200169] [1,219,487.11985]


Benchmark\ReadWholeObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000115878582] [86,297.22445]
    arrayCast : [10,000    ] [0.0000046281338] [216,069.81321]
    closure   : [10,000    ] [0.0000032783508] [305,031.41727]


Benchmark\WritePropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000019083738] [524,006.34659]
    closure   : [10,000    ] [0.0000020534754] [486,979.29849]


Benchmark\WriteSinglePropertyEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000005626678] [1,777,247.45763]
    closure   : [10,000    ] [0.0000009505272] [1,052,047.75760]
```


## Contribute

Please provide fixes and improvements through pull requests.

Don't forget to update the results in the README.

### TODO

- Add Vagrant config for a standard test environment


## Credits

This is merely a compilation of the methods Marco Pivetta exposed on [his blog](http://ocramius.github.io/).
