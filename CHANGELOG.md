### 2.0.3

#### Bugfixes

* `descend('')` was leaving the Uri in an incoherent state

### 2.0.2

#### Bugfixes

* Incorrect `setPathTrail` signature was crashing in PHP 8

### 2.0.1

#### Bugfixes

* `AbstractUri::makeRelativeToBase()` now throws an error instead of silently 
  returning an absolute URI when the relative URI would start with a `/` (eg. 
  `/a//b` relative to `/a/`)

# 2.0.0

#### Changes

`PHPUri` renamed to `PhpUri`

#### New features

* Introduced `UnixPathUri` class
* Introduced full ascend/descend semantics
* Introduced `AbstractUri::getUriStringWithoutQueryFragment()`
  
#### Bugfixes

* Introduced validation for URI schemes
  
#### Improvements

* Made mutators cursive
* Performance improvements for `ascend()` / `descend()`
* Performance improvement: added caching for string generation
* Increased test coverage