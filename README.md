var_representation_polyfill
=============================

[![Build Status](https://github.com/TysonAndre/var_representation_polyfill/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/TysonAndre/var_representation_polyfill/actions/workflows/main.yml?query=branch%3Amain)
[![License](https://img.shields.io/github/license/TysonAndre/var_representation_polyfill.svg)](https://github.com/TysonAndre/var_representation_polyfill/blob/main/LICENSE)

[var_representation_polyfill](https://github.com/TysonAndre/var_representation_polyfill) is a polyfill for https://pecl.php.net/var_representation

This provides a polyfill for the function `var_representation(mixed $value, int $flags = 0): string`, which converts a
variable to a string in a way that fixes the shortcomings of `var_export()`

See [var_representation](https://github.com/TysonAndre/var_representation) documentation for more details

Installation
------------

```
composer require tysonandre/var_representation_polyfill
```

Usage
-----

```php
// uses short arrays, and omits array keys if array_is_list() would be true
php > echo var_representation(['a','b']);
[
  'a',
  'b',
]
// can dump everything on one line.
php > echo var_representation(['a', 'b', 'c'], VAR_REPRESENTATION_SINGLE_LINE);
['a', 'b', 'c']
php > echo var_representation("uses double quotes: \$\"'\\\n");
"uses double quotes: \$\"'\\\n"
```
