<?php

    namespace Test\Exteon\Uri;

    use Exteon\Uri\Uri;
    use PHPUnit\Framework\TestCase;

    class UriTest extends TestCase
    {
        public function testEncoding(): void
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
            self::assertSame($uri->getScheme(), $uri2->getScheme());
            self::assertSame($uri->getHost(), $uri2->getHost());
            self::assertSame($uri->getPort(), $uri2->getPort());
            self::assertSame($uri->getUser(), $uri2->getUser());
            self::assertSame($uri->getPass(), $uri2->getPass());
            self::assertSame($uri->getPath(), $uri2->getPath());
            self::assertSame($uri->getFragment(), $uri2->getFragment());
            self::assertSame($uri->getQueryString(), $uri2->getQueryString());
        }
    }
