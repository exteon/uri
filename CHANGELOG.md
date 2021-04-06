# 2.0.0

## Changes

`PHPUri` renamed to `PhpUri`

## New features

* Introduced `UnixPathUri` class
* Introduced full ascend/descend semantics
* Introduced `AbstractUri::getUriStringWithoutQueryFragment()`
  
## Bugfixes

* Introduced validation for URI schemes
  
## Improvements

* Made mutators cursive
* Performance improvements for `ascend()` / `descend()`
* Performance improvement: added caching for string generation
* Increased test coverage