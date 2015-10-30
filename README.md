CSV File Loader
===============

Load CSV files using PHP generators. It uses memory like `fopen` but requires much less code.

How to Install
--------------

### Using [Composer](http://getcomposer.org/)

1.  Install the `luchaninov/csv-file-loader` package:

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

Advanced Usage
--------------

If you use TSV instead of CSV simply use `TsvFileLoader`.

If you have custom delimiters use `setDelimiter` like `$loader->setDelimiter(';')`. Same with encloser - `setEncloser`.

By default it assumes that the first row of the file is headers - it doesn't return it as item but uses as keys for next rows.
If you don't have headers in the first row - you can:
- set your own keys - `setHeaders(['key1', 'key2', ...])`
- use numerical keys (`[0, 1, 2, ...]`) - `setHeaders(false)`

If there are more cols in some rows than there are cols in headers then numerical keys are added.

You can use same loader to load several files - `$loader->setFilename('other_file.csv')`. If you iterate during some file during
`setFilename` then there will be no more items from the first file, foreach will just finish.

Code is very simple - look at sources and tests.