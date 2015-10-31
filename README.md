CSV File Loader
===============

Load CSV files using PHP generators. It uses memory like `fopen` but requires less code.

[![Build Status](https://travis-ci.org/luchaninov/csv-file-loader.svg?branch=master)](https://travis-ci.org/luchaninov/csv-file-loader)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bc1bdf45-61af-441a-adc7-1e6bb3c6c52f/mini.png)](https://insight.sensiolabs.com/projects/bc1bdf45-61af-441a-adc7-1e6bb3c6c52f)

How to Install
--------------

Install the `luchaninov/csv-file-loader` package using [composer](http://getcomposer.org/):

```shell
$ composer require "luchaninov/csv-file-loader:1.*"
```

Basic Usage
-----------

```php
$loader = new CsvFileLoader();
$loader->setFilename('/path/to/your_data.csv');

foreach ($loader->getItems() as $item) {
    var_dump($item); // do something here
}
```

If you have CSV-file

```
id,name,surname
1,Jack,Black
2,John,Doe
```

you'll get 2 items
```
['id' => '1', 'name' => 'Jack', 'surname' => 'Black']
['id' => '2', 'name' => 'John', 'surname' => 'Doe']
```

It uses [fgetcsv](http://php.net/fgetcsv) function so it understands enclosed values like

```
item1,"item2,still item2",item3
```

and even

```
item1,"item2
still item2",item3
```

Advanced Usage
--------------

If you have TSV instead of CSV simply use `TsvFileLoader`.

If you have custom delimiters use `setDelimiter` like `$loader->setDelimiter(';')`. Same with encloser - `setEncloser`.
Default delimiter is `,` for `CsvFileLoader` and `\t` for `TsvFileLoader`; default encloser is `"`.

By default it assumes that the first row of the file contains headers - it doesn't return this row as item but uses as keys for next rows.
If you don't have headers in the first row - you can:
- set your own keys - `setHeaders(['key1', 'key2', ...])`
- use numerical keys `[0, 1, 2, ...]` - `setHeaders(false)`

If there are more cols in some rows than there are cols in headers then numerical keys are added.

You can use same loader to load several files - `$loader->setFilename('other_file.csv')`. If you iterate during some file during
`setFilename` then there will be no more items from the first file, foreach will just finish.

Code is very simple - look at sources and tests.
