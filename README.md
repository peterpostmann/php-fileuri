# fileuri

[![Software License][ico-license]](LICENSE.md)

Returns a file uri from a (relative) path

## Install

### Via Composer

```bash
composer require peterpostmann/fileuri
```

## Why

This function can be used to create [RFC3986][1] complient file URIs and build protocol agnostic functions.

~~~PHP

function parseReference($ref)
{
    list($prefix, $path) = explode('://', $ref, 2);
    return [$prefix, $path];
}

var_dump(parseReference('https://server.tld/path/ressource.ext'));
var_dump(parseReference('file:///path/to/file.ext'));

~~~


The above example will output:

```

array (size=2)
  0 => string 'https' (length=5)
  1 => string 'server.tld/path/ressource.ext' (length=29)


array (size=2)
  0 => string 'file' (length=4)
  1 => string '/path/to/file.ext (length=17)

```

If you want to examine URIs of multiple protocols this cannot be done easily because PHP does not return rfc3986 compliant URIs for files. PHP returns different formats depending on the file location and platform (http://php.net/manual/en/wrappers.file.php)

~~~PHP

use Sabre\Uri;

function parseReference($uri)
{
    return Uri\parse($uri);
}

~~~

## Usage

~~~PHP

string fileuri ( string path [, string basePath] ) 

~~~

### Example


### Sample Output


~~~PHP

// Absolute Path
echo fileuri('/path/to/file.ext');
echo fileuri('C:\path\to\winfile.ext');
echo fileuri('\\\\smbserver\share\path\to\winfile.ext');

// Relative Path with base path
echo fileuri('relative/path/to/file.ext', '/');
echo fileuri('fileInCwd.ext','C:\testfolder);

// Path that is already a URI
echo fileuri('file:///path/to/file.ext');

~~~


The above example will output:

```

file:///path/to/file.ext
file:///C:/path/to/winfile.ext
file:///C:/path/to/winfile.ext
file://smbserver/share/path/to/winfile.ext
file:///relative/path/to/file.ext
file:///C:/testfolder/fileInCwd.ext
file:///path/to/file.ext

```

### Error Output

The function returns false if a relative path is given without a base path. 

~~~PHP

// Relative Path without base path
var_dump(fileuri('relative/path/to/file.ext'));

~~~


The above example will output:

```

boolean false

```

### Usage with file functions

~~~PHP

// Absolute Path
$uri = fileuri('/path/to/file.ext');

var_dump(file_get_contents(urldecode($uri)));

~~~

`file_get_contents` does not normalize Urls, therefore file URIs cannot be used directly.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[1]: https://tools.ietf.org/html/rfc3986/