var_representation_polyfill
=============================

[![Build Status](https://github.com/TysonAndre/var_representation_polyfill/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/TysonAndre/var_representation_polyfill/actions/workflows/main.yml?query=branch%3Amain)

[var_representation_polyfill](https://github.com/TysonAndre/var_representation_polyfill) is a polyfill for https://pecl.php.net/var_representation

This provides a polyfill for the function `var_representation(mixed $value, int $flags = 0): string`, which converts a
variable to a string in a way that fixes the shortcomings of `var_export()`
