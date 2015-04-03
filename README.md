# phpIgbinary

## What 

Implements a reader in PHP for data serialized with `igbinary` extension using function `igbinary_serialize`. 


## Why
This was implemented with goal to make it more easy to understand and debug (in context of application) how references 
in arrays behave when serialized.
 
## How
Nothing fancy - uses `unpack()` for unpacking and then stores unpacked into some abstractions for data types. A distinction 
between scalar (long, double, string, etc.) and compound (arrays and objects) values is preserved. References are 
resolved as special value type (reference tag) and are on the same level of abstraction as other elements. Note that although
strings share similar loading mechanism (i.e. tags), they are resolved on the spot due to this being not interesting for 
initial goal.

Most complex code is related to compound objects and reference handling. Everything else is pretty straightforward.
 
## Usage

Reader itself is in class `\KristsK\PhpIgbinary\Reader`. It takes serialized data as only parameter in constructor. Data
is processed on instantiation. If something goes wrong, it throws `\KristsK\PhpIgbinary\Exception`. Root of unpacked data then is 
accessible via `getRootElement()` method.
  
A little helper class `\KristsK\PhpIgbinary\Reader\Printer` is implemented. It takes reader instance and two functions
actually doing the printing.
  
Reader instantiation and use example:
   
```php
$printer = new \KristsK\PhpIgbinary\Reader\Printer(
    $reader,
    function ($m) { echo($m); },
    function () { echo("\n"); }
);
$printer->prettyPrint($reader->getRootElement());
```

## Command line session file reader

A very rudimentary session file reader is available in `bin/read-session.php`. It takes session filename as first 
argument, creates reader and printer and then spawns [`Psysh`](http://psysh.org) REPL session.

## Tests

Some minimal test cases are provided in `tests/ReaderTest.php`.

## Licensing

See LICENSE.
 
 
