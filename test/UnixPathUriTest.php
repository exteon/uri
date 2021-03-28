<?php

    namespace Test\Exteon\Uri;

    use ErrorException;
    use Exception;
    use Exteon\Uri\UnixPathUri;
    use PHPUnit\Framework\TestCase;

    class UnixPathUriTest extends TestCase
    {
        /**
         * @throws Exception
         */
        public function testComposeWith(): void
        {
            $baseUri = UnixPathUri::fromString(
                '/root/'
            );

            $uri = UnixPathUri::fromString('a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('/root/a', $uri->toString());

            $uri = UnixPathUri::fromString('/a');
            $uri->composeWithBase($baseUri);
            self::assertEquals('/a', $uri->toString());

            $uri = UnixPathUri::fromString('');
            $uri->composeWithBase($baseUri);
            self::assertEquals(
                '/root/',
                $uri->toString()
            );

            $baseURI = UnixPathUri::fromString('/root');

            $uri = UnixPathUri::fromString('a');
            $uri->composeWithBase($baseURI);
            self::assertEquals('/root/a', $uri->toUriString());

            $uri = UnixPathUri::fromString('');
            $uri->composeWithBase($baseURI);
            self::assertEquals(
                '/root',
                $uri->toUriString()
            );
        }

        /**
         * @throws Exception
         */
        public function testMakeRelative(): void
        {
            $baseURI = UnixPathUri::fromString('/a/b');

            $uri = UnixPathUri::fromString('/a/b');
            $uri->makeRelativeToBase($baseURI);
            self::assertEquals('', $uri->toUriString());

            $uri = UnixPathUri::fromString('/a/c');
            $uri->makeRelativeToBase($baseURI);
            self::assertEquals('/a/c', $uri->toUriString());

            $uri = UnixPathUri::fromString('/a/b/c');
            $uri->makeRelativeToBase($baseURI);
            self::assertEquals('c', $uri->toUriString());

            $uri = UnixPathUri::fromString('/a/b/c/');
            $uri->makeRelativeToBase($baseURI);
            self::assertEquals('c/', $uri->toUriString());
        }

        public function testDescend(): void
        {
            self::assertEquals(
                'a',
                UnixPathUri::fromString('')->descend('a')->toString()
            );
            self::assertEquals(
                '/a',
                UnixPathUri::fromString('/')->descend('a')->toString()
            );
            self::assertEquals(
                'a/b',
                UnixPathUri::fromString('a')
                    ->descend('b')->toString()
            );
            self::assertEquals(
                'a/b',
                UnixPathUri::fromString('a/')
                    ->descend('b')->toString()
            );
        }

        /**
         * @throws ErrorException
         */
        public function testAscend(): void
        {
            self::assertEquals(
                '/a/',
                UnixPathUri::fromString('/a/b/')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '/a/',
                UnixPathUri::fromString('/a/b')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '/',
                UnixPathUri::fromString('/a')
                    ->ascend()->toString()
            );
            self::assertEquals(
                '',
                UnixPathUri::fromString('a')
                    ->ascend()->toString()
            );
        }

        public function testGetUnixPath(): void
        {
            self::assertEquals(
                '/a/b',
                UnixPathUri::fromString('/a/b/')->getUnixPath()
            );
            self::assertEquals(
                'a/b',
                UnixPathUri::fromString('a/b/')->getUnixPath()
            );
            self::assertEquals(
                '^',
                (new UnixPathUri)->setPath('^')->getUnixPath()
            );
        }
    }
