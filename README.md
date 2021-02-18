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
yield `bar` and `foo/bar` respectively.

## PHP URIs

With `Uri`, the query string is not assigned any special meaning. You can use
the `PHPUri` object to parse and generate PHP query strings:

```php
use Exteon\Uri\PHPUri;

$initial = '?foo=bar';
$uri = PhpUri::fromString($initial);
var_dump($uri->getQuery());
// ['foo' => 'bar']
```

```php
use Exteon\Uri\PHPUri;

$uri = new PHPUri();
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