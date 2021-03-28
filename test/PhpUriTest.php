<?php

    namespace Test\Exteon\Uri;

    use Exception;
    use Exteon\Uri\PhpUri;
    use InvalidArgumentException;
    use PHPUnit\Framework\TestCase;

    class PhpUriTest extends TestCase
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

        /**
         * @throws Exception
         */
        public function testComposeWith(): void
        {
            $baseURI = PhpUri::fromString('/?foo[]=bar&foo[]=baz');

            $uri = PhpUri::fromString('#a');
            $uri->composeWithBase($baseURI);
            self::assertEquals(['foo' => ['bar', 'baz']], $uri->getQuery());
            self::assertEquals(
                '/?foo%5B0%5D=bar&foo%5B1%5D=baz#a',
                $uri->toString()
            );
        }

        public function testCompose(): void
        {
            $uri = new PhpUri(
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

            $uri = new PhpUri();

            $uri->setQuery(PhpUri::nullValuesToEmptyString($query));
            self::assertEquals(
                '?a=5&b%5Bx%5D=&b%5By%5D=&b%5Bz%5D=1&c=&d=',
                $uri->toString()
            );

            $uri->setQuery(PhpUri::nullValuesUnset($query));
            self::assertEquals(
                '?a=5&b%5Bx%5D=&b%5Bz%5D=1&d=',
                $uri->toString()
            );
        }

        public function testNullValuesValidation(): void
        {
            $this->expectException(InvalidArgumentException::class);
            $uri = new PhpUri();
            $uri->setQuery(['a' => ['b' => null]]);
        }


        public function testSchemeValidationOnConstructorArg()
        {
            $this->expectException(InvalidArgumentException::class);
            new PhpUri('/test:');
        }

        public function testSchemeValidationOnSetScheme()
        {
            $this->expectException(InvalidArgumentException::class);
            (new PhpUri())->setScheme('/test:');
        }

    }
