<?php

    namespace Exteon\Uri;

    use InvalidArgumentException;

    class UnixPathUri extends AbstractUri
    {
        use PathTrailTrait;

        /**
         * URI constructor.
         * @param string $path
         */
        public function __construct(
            string $path = ''
        ) {
            $this->setPath($path);
        }

        public static function isTrailingSlashInsensitive(): bool
        {
            return true;
        }

        /**
         * @param string|null $scheme
         * @return static
         */
        public function setScheme(
            ?string $scheme
        ): AbstractUri {
            if ($scheme !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have scheme'
                );
            }
            return $this;
        }

        /**
         * @param string|null $fragment
         * @return AbstractUri
         */
        public function setFragment(?string $fragment): AbstractUri
        {
            if ($fragment !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have fragment'
                );
            }
            return $this;
        }

        /**
         * @param string|null $queryString
         * @return static
         */
        public function setQueryString(?string $queryString): AbstractUri
        {
            if ($queryString !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have query strings'
                );
            }
            return $this;
        }

        /**
         * @return string|null
         */
        public function getQueryString(): ?string
        {
            return null;
        }

        /**
         * @return string|null
         */
        public function getScheme(): ?string
        {
            return null;
        }

        /**
         * @return string|null
         */
        public function getHost(): ?string
        {
            return null;
        }

        /**
         * @param string|null $host
         * @return static
         */
        public function setHost(
            ?string $host
        ): AbstractUri {
            if ($host !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have host, port, or credentials'
                );
            }
            return $this;
        }

        /**
         * @return int|null
         */
        public function getPort(): ?int
        {
            return null;
        }

        /**
         * @param int|null $port
         * @return static
         */
        public function setPort(
            ?int $port
        ): AbstractUri {
            if ($port !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have host, port, or credentials'
                );
            }
            return $this;
        }

        /**
         * @return string|null
         */
        public function getUser(): ?string
        {
            return null;
        }

        /**
         * @param string|null $user
         * @return static
         */
        public function setUser(
            ?string $user
        ): AbstractUri {
            if ($user !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have host, port, or credentials'
                );
            }
            return $this;
        }

        /**
         * @return string|null
         */
        public function getPass(): ?string
        {
            return null;
        }

        /**
         * @param string|null $pass
         * @return AbstractUri
         */
        public function setPass(?string $pass): AbstractUri
        {
            if ($pass !== null) {
                throw new InvalidArgumentException(
                    'Unix path URIs cannot have host, port, or credentials'
                );
            }
            return $this;
        }

        /**
         * @return string|null
         */
        public function getFragment(): ?string
        {
            return null;
        }

        public function getUnixPath(): string
        {
            return
                ($this->isPathRooted ? '/' : '') .
                implode('/', $this->pathTrail);
        }

        public function getDirectoryPathTrail(): array
        {
            return $this->getPathTrail();
        }

        public function hasDirectory(): bool
        {
            return $this->hasPath();
        }
    }