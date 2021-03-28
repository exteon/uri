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
            self::assertEquals($uri, $uri2);
        }
    }
