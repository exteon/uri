<?php

    namespace Test\Exteon\Uri;

    use Exception;
    use Exteon\Uri\Uri;
    use PHPUnit\Framework\TestCase;

    class UriTest extends TestCase
    {
        public function testNoSchemeUnqualified(): void
        {
            $initial = '';
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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

        public function testNoSchemeQualified(): void
        {
            $initial = '//host';
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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

        public function testSchemeUnqualified(): void
        {
            $initial = 'scheme:';
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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

        public function testSchemeQualified(): void
        {
            $initial = 'scheme://host';
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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
            $uri = Uri::fromString($initial);
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

        public function testEmptyScheme(): void
        {
            $initial = ':';
            $uri = Uri::fromString($initial);
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
         * @throws Exception
         */
        public function testComposeWith(): void
        {
            $baseUri = Uri::fromString('scheme://host/root/?query#fragment');

            $uri = Uri::fromString('a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/a', $uri->toString());

            $uri = Uri::fromString('/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/a', $uri->toString());

            $uri = Uri::fromString('//host2/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('//host2/a', $uri->toString());

            $uri = Uri::fromString('://host2/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host2/a', $uri->toString());

            $uri = Uri::fromString('');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query#fragment',
                $uri->toString()
            );

            $uri = Uri::fromString('#fragment2');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query#fragment2',
                $uri->toString()
            );

            $uri = Uri::fromString('?query2');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/?query2', $uri->toString());

            $uri = Uri::fromString('?query2#fragment2');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                'scheme://host/root/?query2#fragment2',
                $uri->toString()
            );

            $baseUri = Uri::fromString('scheme://host/root/a?query#fragment');

            $uri = Uri::fromString('b');
            $uri->composeWithBase($baseUri);
            self::assertEquals('scheme://host/root/b', $uri->toString());

        }

        /**
         * @throws Exception
         */
        public function testMakeRelative(): void
        {
            $baseUri = Uri::fromString('scheme://host/a/b?query#fragment');

            $uri = Uri::fromString('scheme://host/a/b?query#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('', $uri->toString());
            
            $uri = Uri::fromString('scheme://host/a/b?query');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('#', $uri->toString());
            
            $uri = Uri::fromString('scheme://host/a/b');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?', $uri->toString());
            
            $uri = Uri::fromString('scheme://host/a/b#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?#fragment', $uri->toString());

            $uri = Uri::fromString('scheme://host/a/b?query#fragment2');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('#fragment2', $uri->toString());

            $uri = Uri::fromString('scheme://host/a/b?query2#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('?query2#fragment', $uri->toString());

            $uri = Uri::fromString('scheme://host/a/c?query#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('c?query#fragment', $uri->toString());

            $uri = Uri::fromString('scheme://host/a?query#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('/a?query#fragment', $uri->toString());

            $uri = Uri::fromString('scheme://host2/a/b?query#fragment');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals('://host2/a/b?query#fragment', $uri->toString());

            $baseUri = Uri::fromString('scheme:a/b');

            $uri = Uri::fromString('scheme:a');
            $uri->makeRelativeToBase($baseUri);
            self::assertEquals(':a', $uri->toString());

        }

        public function testEncoding()
        {
            $uri = new Uri(
                's-c+h.eme',
                'ă î',
                1234,
                'ă î',
                'ă î',
                '/ă î',
                'ă î',
                'ă î'
            );
            $uriString = $uri->toString();
            self::assertEquals(
                's-c+h.eme://%C4%83%20%C3%AE:%C4%83%20%C3%AE@%C4%83%20%C3%AE:1234/%C4%83%20%C3%AE?ă î#ă î',
                $uriString
            );
            $uri2 = Uri::fromString($uriString);
            self::assertEquals($uri, $uri2);
        }

    }
