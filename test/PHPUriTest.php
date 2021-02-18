<?php

    namespace Test\Exteon\Uri;

    use Exteon\Uri\PHPUri;
    use InvalidArgumentException;
    use PHPUnit\Framework\TestCase;

    class PHPUriTest extends TestCase
    {
        public function testQueryString(): void
        {
            $initial = '?foo=bar';
            $uri = PhpUri::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals(['foo' => 'bar'], $uri->getQuery());

            $initial = '?foo%5B0%5D=bar&foo%5B1%5D=baz';
            $uri = PhpUri::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals(['foo' => ['bar', 'baz']], $uri->getQuery());
        }

        public function testComposeWith(): void
        {
            $baseUri = PhpUri::fromString('/?foo[]=bar&foo[]=baz');

            $uri = PhpUri::fromString('#a');
            $uri->composeWithBase($baseUri);
            self::assertEquals(['foo' => ['bar', 'baz']], $uri->getQuery());
            self::assertEquals(
                '/?foo%5B0%5D=bar&foo%5B1%5D=baz#a',
                $uri->toString()
            );
        }

        public function testCompose(): void
        {
            $uri = new PHPUri(
                null,
                null,
                null,
                null,
                null,
                '',
                ['foo' => ['bar', 'baz']],
                null
            );
            self::assertEquals(
                '?foo%5B0%5D=bar&foo%5B1%5D=baz',
                $uri->toString()
            );
        }

        public function testNullValues(): void
        {
            $query = [
                'a' => 5,
                'b' => [
                    'x' => '',
                    'y' => null,
                    'z' => 1
                ],
                'c' => null,
                'd' => ''
            ];

            $uri = new PHPUri();

            $uri->setQuery(PHPUri::nullValuesToEmptyString($query));
            self::assertEquals(
                '?a=5&b%5Bx%5D=&b%5By%5D=&b%5Bz%5D=1&c=&d=',
                $uri->toString()
            );

            $uri->setQuery(PHPUri::nullValuesUnset($query));
            self::assertEquals(
                '?a=5&b%5Bx%5D=&b%5Bz%5D=1&d=',
                $uri->toString()
            );
        }

        public function testNullValuesValidation(): void
        {
            $this->expectException(InvalidArgumentException::class);
            $uri = new PHPUri();
            $uri->setQuery(['a' => ['b' => null]]);
        }
    }
