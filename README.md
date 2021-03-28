# exteon/uri

Provides a set of objects for parsing, composing and manipulating URIs

Parsing an URI string:

```php
use Exteon\Uri\Uri;

$uri = Uri::fromString('http://user:pass@foo.bar:8080/path/to?query#fragment');
var_dump(
    $uri->getScheme(),
    $uri->getUser(),
    $uri->getPass(),
    $uri->getHost(),
    $uri->getPort(),
    $uri->getPath(),
    $uri->getQueryString(),
    $uri->getFragment()
);
```

Generating an URI string:

```php
use Exteon\Uri\Uri;

$uri = new Uri(
    'http',
    'foo.bar',
    '8080',
    'user',
    'pass',
    'path/to',
    'query',
    'fragment'
);

var_dump($uri->toString());
```

***Note***

The magic `__toString()` method is not implemented, I find its meaning to be
semantically inconsistent so use the explicit `toString()`.

## URI manipulation

***Note***

`Uri` objects are not immutable; make sure you `clone` where appropriate before
performing manipulation.

### Setters

You can use 
`setScheme(), setUser(), setPass(), setHost(), setPort(), setPath(), setQueryString(), setFragment()`
setters to modify an URI.

### Base and relative urls, composition

The `Uri` object can be used to combine relative URIs to a base URI:

```php
use Exteon\Uri\Uri;

$baseUri = Uri::fromString('scheme://host/root/?query#fragment');

$uri = Uri::fromString('a');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/a

$uri = Uri::fromString('/a');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/a

$uri = Uri::fromString('://host2/a');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host2/a

$uri = Uri::fromString('');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/?query#fragment

$uri = Uri::fromString('#fragment2');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/?query#fragment2

$uri = Uri::fromString('?query2');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/?query2

$uri = Uri::fromString('?query2#fragment2');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/?query2#fragment2

$baseUri = Uri::fromString('scheme://host/root/a?query#fragment');

$uri = Uri::fromString('b');
$uri->composeWithBase($baseUri);
echo $uri->toString();
// scheme://host/root/b
```

The converse to `composeWithBase()` is `applyRelative()`, which is perfectly 
symmetrical when run on the base URI with a relative URI as parameter. 

A relative URL can also be derived from an absolute URL and a base URL:

```php
use Exteon\Uri\Uri;

$baseUri = Uri::fromString('scheme://host/a/b?query#fragment');

$uri = Uri::fromString('scheme://host/a/b?query#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
//

$uri = Uri::fromString('scheme://host/a/b?query');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// #

$uri = Uri::fromString('scheme://host/a/b');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// ?

$uri = Uri::fromString('scheme://host/a/b#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// ?#fragment

$uri = Uri::fromString('scheme://host/a/b?query#fragment2');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// #fragment2

$uri = Uri::fromString('scheme://host/a/b?query2#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// ?query2#fragment

$uri = Uri::fromString('scheme://host/a/c?query#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// c?query#fragment

$uri = Uri::fromString('scheme://host/a?query#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// /a?query#fragment

$uri = Uri::fromString('scheme://host2/a/b?query#fragment');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// ://host2/a/b?query#fragment

$baseUri = Uri::fromString('scheme:a/b');

$uri = Uri::fromString('scheme:a');
$uri->makeRelativeToBase($baseUri);
echo $uri->toString();
// :a
```

***Note***

`Uri` will allow parsing and generate relative empty-scheme URIs of the form 
`://host/path`, which are not allowed by 
[RFC 3986](https://tools.ietf.org/html/rfc3986) but are a regular occurence in 
browser urls to specify 'same protocol as browsed page'.

***Note***

Be mindful about trailing `/` in paths; the library uses the browser composition
algorithm: a relative url of `bar` composed with base urls `foo` and `foo/` will
yield `bar` and `foo/bar` respectively. The exception to this is the 
[`UnixPathUri`](#unix-path-uri).

### `ascend()` and `descend()`

While not present in [RFC 3986](https://tools.ietf.org/html/rfc3986), it has 
become quite ubiquitous that the path component of an URI is a trail of 
hierarchically organized components separated by `/`. To serve this convention,
all URI objects implement the `ascend()` and `descend()` functions.

```php
public function ascend(int $levels = 1): self;
```

```php
use Exteon\Uri\Uri;

$uri = Uri::fromString('a/b?qs');
$uri->ascend();
var_dump($uri->toString());
// a/
```

***Note***

When using `ascend()`, the resulting URI will always be considered a directory and 
have the trailing slash.

```php
public function descend(string $path): self;
```

```php
use Exteon\Uri\Uri;

$uri = Uri::fromString('a/b?qs');
$uri->descend('c/d');
var_dump($uri->toString());
// a/c/d
```

***Note***

When using `descend()`, the Web convention, applying the descending path to the
URI directory, as in the example above. Because `a/b` does not include a 
trailing slash, the directory part is `a/` to which `c/d` is applied.

The exception to the above described behavior is the 
[`UnixPathUri`](#unix-path-uri).

## PHP URIs

With `Uri`, the query string is not assigned any special meaning. You can use
the `PHPUri` object to parse and generate PHP query strings:

```php
use Exteon\Uri\PhpUri;

$initial = '?foo=bar';
$uri = PhpUri::fromString($initial);
var_dump($uri->getQuery());
// ['foo' => 'bar']
```

```php
use Exteon\Uri\PhpUri;

$uri = new PhpUri();
$uri->setQuery(['foo' => 'bar']);
echo $uri->toString();
// ?foo=bar
```

***Note***

PHP's `http_build_query()` silently discards keys for NULL values in query 
string aggregation. For consistency's sake, you must explicitly handle 
null values. For this `PHPUri` provides two helper methods: 
`nullValuesToEmptyString()` and `nullValuesUnset()` (the default PHP behavior) 
which you can use on the query before passing to the constructor or 
`setQuery()`. If an array with null values is passed to `setQuery()`, an 
`InvalidArgumentException` will be thrown.

## <a name="unix-path-uri"></a>Unix path URIs

The `UnixPathUri` models an URI that can only have a path part (no scheme, host,
query string, fragment). They can be composed just like the Web URIs, but with a
quite important different convention:

For `UnixPathUri`, `getDirectory()` and `getPath()` represent the same resource
even if the URI does not have a trailing slash.

Example:

```php
use Exteon\Uri\UnixPathUri;

$uri = UnixPathUri::fromString('/a/b');
var_dump($uri->getPath());
// /a/b
var_dump($uri->getDirectory());
// /a/b/
// Uri::getDirectory() would have been "/a/" 
```

This has implications on the way relative URIs are applied and derived. For 
instance:

```php
use Exteon\Uri\UnixPathUri;

$base = UnixPathUri::fromString('/a');
$relative = UnixPathUri::fromString('b');
$relative->composeWithBase($base);
var_dump($relative->toString());
// /a/b
// With Uri, the result would have been "/b" 
```

In a more intuitive way, the `UnixPathUri` URI type acts just like a Unix shell 
path, and the relative URIs act just like running a `cd` command on the base 
URI. Special path fragments like `.` and `..` are obviously not implemented,
you can use the `ascend()` method to model going to an upper dir.

`UnixPathUri` can be combined with other types of URIs (i.e. a relative 
`UnixPathUri` can be combined with a root web `Uri` via `Uri::applyRelative()`)
resulting in a full Web Uri.