<?php

    namespace Test\Exteon\Uri;

    use ErrorException;
    use Exception;
    use Exteon\Uri\AbstractUri;
    use Exteon\Uri\PhpUri;
    use Exteon\Uri\UnixPathUri;
    use Exteon\Uri\Uri;
    use InvalidArgumentException;
    use PHPUnit\Framework\TestCase;

    class AbstractUriTest extends TestCase
    {
        /**
         * @return string[]
         */
        function getDerivedClasses1(): array
        {
            return [
                [Uri::class],
                [PhpUri::class],
                [UnixPathUri::class]
            ];
        }

        /**
         * @return string[]
         */
        function getDerivedClasses2(): array
        {
            return [
                [Uri::class],
                [PhpUri::class]
            ];
        }


        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testNoSchemeUnqualified(string $uriType): void
        {
            $initial = '';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertFalse($uri->isRooted());
            self::assertFalse($uri->isQualified());


            $initial = 'foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertFalse($uri->isRooted());
            self::assertFalse($uri->isQualified());

            $initial = '/foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('/foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertFalse($uri->isQualified());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testNoSchemeQualified(string $uriType): void
        {
            $initial = '//host';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());

            $initial = '//host/';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('/', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());

            $initial = '//host/foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertNull($uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('/foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testSchemeUnqualified(string $uriType): void
        {
            $initial = 'scheme:';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());


            $initial = 'scheme:foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());

            $initial = 'scheme:/foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('/foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testSchemeQualified(string $uriType): void
        {
            $initial = 'scheme://host';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());

            $initial = 'scheme://host/';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('/', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());


            $initial = 'scheme://host/foo';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertEquals('scheme', $uri->getScheme());
            self::assertEquals('host', $uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertEquals('/foo', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testEmptyScheme(string $uriType): void
        {
            $initial = ':';
            $uri = $uriType::fromString($initial);
            $recomposed = $uri->toString();
            self::assertEquals($initial, $recomposed);
            self::assertSame('', $uri->getScheme());
            self::assertNull($uri->getHost());
            self::assertNull($uri->getPort());
            self::assertNull($uri->getUser());
            self::assertNull($uri->getPass());
            self::assertSame('', $uri->getPath());
            self::assertEmpty($uri->getQueryString());
            self::assertNull($uri->getFragment());

            self::assertTrue($uri->isRooted());
            self::assertTrue($uri->isQualified());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @throws Exception
         * @dataProvider getDerivedClasses2
         */
        public function testComposeWith(string $uriType): void
        {
            $baseUri = $uriType::fromString(
                'scheme://host/root/?query=#fragment'
            );

            $uri = $uriType::fromString('a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/a', $uri->toString());

            $uri = $uriType::fromString('/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/a', $uri->toString());

            $uri = $uriType::fromString('//host2/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('//host2/a', $uri->toString());

            $uri = $uriType::fromString('://host2/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host2/a', $uri->toString());

            $uri = $uriType::fromString('');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query=#fragment',
                $uri->toString()
            );

            $uri = $uriType::fromString('#fragment2');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query=#fragment2',
                $uri->toString()
            );

            $uri = $uriType::fromString('?query2=');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/?query2=', $uri->toString());

            $uri = $uriType::fromString('?query2=#fragment2');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query2=#fragment2',
                $uri->toString()
            );

            $baseUri = $uriType::fromString(
                'scheme://host/root/a?query=#fragment'
            );

            $uri = $uriType::fromString('b');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/b', $uri->toString());
        }


        /**
         * @param class-string<AbstractUri> $uriType
         * @throws Exception
         * @dataProvider getDerivedClasses2
         */
        public function testMakeRelative(string $uriType): void
        {
            $baseUri = $uriType::fromString('scheme://host/a/b?query#fragment');

            $uri = $uriType::fromString('scheme://host/a/b?query#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/b?query');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('#', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/b');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/b#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?#fragment', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/b?query#fragment2');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('#fragment2', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/b?query2=#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?query2=#fragment', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a/c?query=#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('c?query=#fragment', $uri->toString());

            $uri = $uriType::fromString('scheme://host/a?query=#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('/a?query=#fragment', $uri->toString());

            $uri = $uriType::fromString('scheme://host2/a/b?query=#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals(
                '://host2/a/b?query=#fragment',
                $uri->toString()
            );

            $baseUri = $uriType::fromString('scheme:a/b');

            $uri = $uriType::fromString('scheme:a');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals(':a', $uri->toString());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testSchemeValidationOnConstructorArg(string $uriType
        ): void {
            $uri = new $uriType();
            $this->expectException(InvalidArgumentException::class);
            $uri->setScheme('/test:');
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testSchemeValidationOnSetScheme(string $uriType): void
        {
            $this->expectException(InvalidArgumentException::class);
            (new $uriType())->setScheme('/test:');
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testSchemeValidationFromString(string $uriType): void
        {
            $uri = $uriType::fromString('/test:');
            self::assertNull($uri->getScheme());
            self::assertEquals('/test:', $uri->getPath());
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testDescend(string $uriType): void
        {
            self::assertEquals(
                'a',
                $uriType::fromString('')->descend('a')->toString()
            );
            self::assertEquals(
                '/a',
                $uriType::fromString('/')->descend('a')->toString()
            );
            self::assertEquals(
                'scheme://user:pass@host/a',
                $uriType::fromString('scheme://user:pass@host')
                    ->descend('a')->toString()
            );
            self::assertEquals(
                'scheme://user:pass@host/b',
                $uriType::fromString('scheme://user:pass@host/a')
                    ->descend('b')->toString()
            );
            self::assertEquals(
                'scheme://user:pass@host/a/b',
                $uriType::fromString('scheme://user:pass@host/a/')
                    ->descend('b')->toString()
            );
            self::assertEquals(
                'scheme://user:pass@host/a/b',
                $uriType::fromString('scheme://user:pass@host/a/?qs#frag')
                    ->descend('b')->toString()
            );
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses2
         */
        public function testAscend(string $uriType): void
        {
            self::assertEquals(
                '/a/',
                $uriType::fromString('/a/b/?qs#frag')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '/a/',
                $uriType::fromString('/a/b?qs#frag')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '/',
                $uriType::fromString('/a?qs#frag')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '',
                $uriType::fromString('a?qs#frag')
                    ->ascend()->toString()
            );
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testCannotAscend1(string $uriType): void
        {
            $this->expectException(ErrorException::class);
            $uriType::fromString('')->ascend();
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testCannotAscend2(string $uriType): void
        {
            $this->expectException(ErrorException::class);
            $uriType::fromString('/')->ascend();
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testCannotAscend3(string $uriType): void
        {
            $this->expectException(ErrorException::class);
            $uriType::fromString('/a')->ascend(2);
        }

        /**
         * @param class-string<AbstractUri> $uriType
         * @dataProvider getDerivedClasses1
         */
        public function testCannotAscendRelative(string $uriType): void
        {
            $this->expectException(ErrorException::class);
            $uriType::fromString('')->ascend();
        }
    }
