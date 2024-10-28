<!--
SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>

SPDX-License-Identifier: GPL-3.0-or-later
-->

Themosis Filesystem
===================

The Themosis filesystem component is a wrapper around PHP filesystem functions.

The library provides a `Filesystem` interface as well as a concrete `LocalFilesystem` implementation.

The `LocalFilesystem` class can only be used to manage "local" files. If you need to deal with remote files, you will need to provide your own implementation of the `Filesystem` interface.

Installation
------------

Install the library using [Composer](https://getcomposer.org/):

```shell
composer require themosis/filesystem
```

Usage
-----

### Read file content

You can read the content of any file by passing its path to the `read()`:

```php
use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();

$content = $filesystem->read('/path/to/file.txt');
```

> The local filesystem implementation is using the PHP [file_get_contents()](https://www.php.net/manual/function.file-get-contents) function.

The implementation is also doing some checks before and after reading the content.

For example, the method is first checking if the file exists before reading it.
If the file does not exist, the method will throw a `FileDoesNotExist` exception.

After accessing the content, the method is checking to see if the content is actually readable.
If the content is somehow corrupted, the method will throw a `ReadFileException` exception.

### Write file content

You can wrtite content to any file by calling the `write()` method.
Pass the path to the file as first parameter and the content as the second parameter:

```php
use Themosis\Components\Filesystem\LocalFilesystem;

$hello = 'Hello World!';

$filesystem = new LocalFilesystem();
$filesystem->write('/path/to/file.txt' , $hello');
```

> The local filesystem implementation is using the PHP [file_put_contents()](https://www.php.net/manual/function.file-put-contents) function.

If the filesystem cannot write the content on the given file, it will throw a `WriteFileException` exception.

### Require PHP file

This is PHP specific. You can require any PHP file using the filesystem `require()` or `requireOnce()` methods.

Each one of them is leveraging the core PHP function of its name with the added option where you can pass and expose variables to the included PHP file.

First example below is just including a PHP file:

```php
<?php

use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();
$filesystem->require(__DIR__ . '/includes/file-a.php');
```

The methods behave just like their core siblings. If the included file is returning data, it will be returned as well by the filesystem require methods:

```php
// File A stored inside project /includes/file-a.php
<?php

return [
    'hello' => 'World!',
];

// Main file requires File A
use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();
$data = $filesystem->require(__DIR__ . '/includes/file-a.php');
```

In above code example, the `$data` variable is containing the array returned by the required file.

#### Passing data

When you require a file using the filesystem, you can pass data to the included file by providing
an array of key/value pairs as a second parameter:

```php
<?php

use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();
$filesystem->require(__DIR__ . '/includes/file-b.php', [
    'hello' => 'World!',
]);

// File B stored inside project /includes/file-b.php
<head>
    <title><?= $hello ?></title>
</head>
```

The `File B` in above code example, is containing HTML content and echoes the `$hello` variable that was passed in the `require()` filesystem method as a second parameter. 

> The API is the same when using the `requireOnce()` method. Watchout the returned value though.

### File exists

The filesystem exposes the `exists()` method to let you check if a file exists:

```php
<?php

use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();

if ( $filesystem->exists('/path/to/file.txt')) {
    // Do something...
}
```

### Check path is a file

You can verify if the given path is targeting a file using the `isFile()` method:

```php
<?php

use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();

if ( $filesystem->isFile('/path/to/file.txt')) {
    // Do something...
}
```

### Check path is a directory

You can verify if the given path is targeting a directory using the `isDirectory()` method:

```php
<?php

use Themosis\Components\Filesystem\LocalFilesystem;

$filesystem = new LocalFilesystem();

if ($filesystem->isDirectory('/path/to/dir')) {
    // Do something...
}
