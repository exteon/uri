<?php

    namespace Exteon\Uri;

    use ErrorException;
    use Exception;
    use InvalidArgumentException;

    abstract class AbstractUri
    {
        /** @var string|null */
        protected $cachedUriString;

        /** @var string|null */
        protected $cachedUriStringWithoutQueryFragment;

        abstract public function __construct();

        /**
         * @param string $uri
         * @return static
         */
        public static function fromString(
            string $uri
        ): self {
            //  Allow for empty scheme; RFC doesn't allow for this, but we allow
            //  "://path" urls to use with composeWith(). Browsers also allow
            //  this kind of URIs.
            $setEmptyScheme = false;
            if (substr($uri, 0, 1) === ':') {
                $uri = substr($uri, 1);
                $setEmptyScheme = true;
            }
            $parsed = parse_url($uri);
            if (!is_array($parsed)) {
                throw new InvalidArgumentException('Invalid URI');
            }
            $result = new static();
            $result
                ->setScheme(
                    $parsed['scheme'] ?? ($setEmptyScheme ? '' : null)
                )
                ->setHost(
                    isset($parsed['host']) ? rawurldecode(
                        $parsed['host']
                    ) : null
                )
                ->setPort(
                    isset($parsed['port']) ? (int)$parsed['port'] : null
                )
                ->setUser(
                    isset($parsed['user']) ? rawurldecode(
                        $parsed['user']
                    ) : null
                )
                ->setPass(
                    isset($parsed['pass']) ? rawurldecode(
                        $parsed['pass']
                    ) : null
                )
                ->setPath(
                    isset($parsed['path']) ? rawurldecode($parsed['path']) : ''
                )
                ->setFragment($parsed['fragment'] ?? null)
                ->setQueryString($parsed['query'] ?? null);
            return $result;
        }

        /**
         * @param string|null $queryString
         * @return static
         */
        abstract public function setQueryString(?string $queryString): self;

        /**
         * @param string|null $fragment
         * @return static
         */
        abstract public function setFragment(?string $fragment): self;

        /**
         * @param string $path
         * @return static
         */
        abstract public function setPath(string $path): self;

        /**
         * @param string|null $pass
         * @return static
         */
        abstract public function setPass(?string $pass): self;

        /**
         * @param string|null $user
         * @return static
         */
        abstract public function setUser(?string $user): self;

        /**
         * @param int|null $port
         * @return static
         */
        abstract public function setPort(?int $port): self;

        /**
         * @param string|null $host
         * @return static
         */
        abstract public function setHost(?string $host): self;

        /**
         * @param string|null $scheme
         * @return static
         */
        abstract public function setScheme(?string $scheme): self;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public static function from(self $uri): self
        {
            if (static::class === get_class($uri)) {
                return clone $uri;
            }
            return
                (new static())
                    ->setSchemeFrom($uri)
                    ->setHostFrom($uri)
                    ->setPortFrom($uri)
                    ->setUserFrom($uri)
                    ->setPassFrom($uri)
                    ->setPathFrom($uri)
                    ->setQueryStringFrom($uri)
                    ->setFragmentFrom($uri);
        }

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setFragmentFrom(self $uri): self
        {
            return $this->setFragment($uri->getFragment());
        }

        /**
         * @return string|null
         */
        abstract public function getFragment(): ?string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setQueryStringFrom(self $uri): self
        {
            return $this->setQueryString($uri->getQueryString());
        }

        /**
         * @return string|null
         */
        abstract public function getQueryString(): ?string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setPathFrom(self $uri): self
        {
            return $this->setPath($uri->getPath());
        }

        /**
         * @return string
         */
        abstract public function getPath(): string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setPassFrom(self $uri): self
        {
            return $this->setPass($uri->getPass());
        }

        /**
         * @return string|null
         */
        abstract public function getPass(): ?string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setUserFrom(self $uri): self
        {
            return $this->setUser($uri->getUser());
        }

        /**
         * @return string|null
         */
        abstract public function getUser(): ?string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setPortFrom(self $uri): self
        {
            return $this->setPort($uri->getPort());
        }

        /**
         * @return int|null
         */
        abstract public function getPort(): ?int;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setHostFrom(self $uri): self
        {
            return $this->setHost($uri->getHost());
        }

        /**
         * @return string|null
         */
        abstract public function getHost(): ?string;

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setSchemeFrom(self $uri): self
        {
            return $this->setScheme($uri->getScheme());
        }

        /**
         * @return string|null
         */
        abstract public function getScheme(): ?string;

        public abstract static function isTrailingSlashInsensitive(): bool;

        /**
         * @param string $scheme
         */
        protected static function validateScheme(string $scheme): void
        {
            if (!preg_match('`^[a-z]([a-z0-9+.-])*$`i', $scheme)) {
                throw new InvalidArgumentException('Invalid scheme');
            }
        }

        public function toString(): string
        {
            return $this->getUriString();
        }

        public function getUriString(): string
        {
            if ($this->cachedUriString === null) {
                $this->cachedUriString = $this->composeUriString();
            }
            return $this->cachedUriString;
        }

        /**
         * @return string
         */
        protected function composeUriString(): string
        {
            $uri = $this->getUriStringWithoutQueryFragment();
            if ($this->hasQueryString()) {
                $uri .= '?' . $this->getQueryString();
            }
            if ($this->hasFragment()) {
                $uri .= '#' . $this->getFragment();
            }
            return $uri;
        }

        public function getUriStringWithoutQueryFragment(): string
        {
            if ($this->cachedUriStringWithoutQueryFragment === null) {
                $this->cachedUriStringWithoutQueryFragment = $this->composeUriStringWithoutQueryFragment(
                );
            }
            return $this->cachedUriStringWithoutQueryFragment;
        }

        /**
         * @return string
         */
        protected function composeUriStringWithoutQueryFragment(): string
        {
            $uri = '';
            if ($this->hasScheme()) {
                $uri .= $this->getScheme() . ':';
            }
            if ($this->hasHost()) {
                $uri .= '//';
                if ($this->hasUser()) {
                    $uri .= rawurlencode($this->getUser());
                    if ($this->hasPass()) {
                        $uri .= ':' . rawurlencode($this->getPass());
                    }
                    $uri .= '@';
                }
                $uri .= rawurlencode($this->getHost());
                if ($this->hasPort()) {
                    $uri .= ':' . rawurlencode($this->getPort());
                }
                if (
                    $this->hasPath() &&
                    $this->getPath()[0] !== '/'
                ) {
                    $uri .= '/';
                }
            }
            if ($this->isPathRooted()) {
                $uri .= '/';
            }
            $pathTrail = $this->getPathTrail();
            $uri .= implode(
                '/',
                array_map(
                    'rawurlencode',
                    $pathTrail
                )
            );
            if (
                $this->hasTrailingSlash() &&
                $pathTrail
            ) {
                $uri .= '/';
            }
            return $uri;
        }

        public function hasScheme(): bool
        {
            return ($this->getScheme() !== null);
        }

        public function hasHost(): bool
        {
            return ($this->getHost() !== null);
        }

        public function hasUser(): bool
        {
            return ($this->getUser() !== null);
        }

        public function hasPass(): bool
        {
            return ($this->getPass() !== null);
        }

        protected function hasPort(): bool
        {
            return ($this->getPort() !== null);
        }

        public function hasPath(): bool
        {
            return ($this->getPath() !== '');
        }

        /**
         * @return bool
         */
        public function isPathRooted(): bool
        {
            return (
                $this->hasDirectory() &&
                substr($this->getDirectory(), 0, 1) === '/'
            );
        }

        public function hasDirectory(): bool
        {
            return ($this->getDirectory() !== '');
        }

        /**
         * Gets the document directory, including the trailing /
         *
         * @return string
         */
        abstract public function getDirectory(): string;

        public function getPathTrail(): array
        {
            $trail = explode('/', $this->getPath());
            if (
                $trail &&
                !end($trail)
            ) {
                array_pop($trail);
            }
            if (
                $trail &&
                !reset($trail)
            ) {
                array_shift($trail);
            }
            return $trail;
        }

        public function hasTrailingSlash(): bool
        {
            return (substr($this->getPath(), -1) === '/');
        }

        /**
         * @return bool
         */
        protected function hasQueryString(): bool
        {
            return ($this->getQueryString() !== null);
        }

        /**
         * @return bool
         */
        public function hasFragment(): bool
        {
            return $this->getFragment() !== null;
        }

        /**
         * @param self $base
         * @return static
         * @throws Exception
         */
        public function composeWithBase(self $base): self
        {
            if (!$base->isRooted()) {
                throw new Exception('Base must be a rooted URI');
            }
            $wasRooted = $this->isRooted();
            $wasQualified = $this->isQualified();
            if (
                $this->hasScheme() &&
                $this->getScheme() !== ''
            ) {
                return $this;
            }
            if (
                $this->getScheme() === '' ||
                (
                    $this->getScheme() === null &&
                    !$this->hasHost()
                )
            ) {
                $this->setSchemeFrom($base);
            }
            if ($wasQualified) {
                return $this;
            }
            if (
                !$wasRooted &&
                !$this->hasPath()
            ) {
                if (!$this->hasQueryString()) {
                    $this->setQueryStringFrom($base);
                    if (!$this->hasFragment()) {
                        $this->setFragmentFrom($base);
                    }
                }
            }
            if (!$wasRooted) {
                if ($this->hasPath()) {
                    $this->setPath($base->getDirectory() . $this->getPath());
                } else {
                    $this->setPathFrom($base);
                }
            }
            return
                $this
                    ->setHostFrom($base)
                    ->setPortFrom($base)
                    ->setUserFrom($base)
                    ->setPassFrom($base);
        }

        /**
         * @return bool
         */
        public function isRooted(): bool
        {
            return (
                $this->isQualified() ||
                $this->isPathRooted()
            );
        }

        /**
         * @return bool
         */
        public function isQualified(): bool
        {
            return (
                $this->hasHost() ||
                $this->hasScheme()
            );
        }

        /**
         * @param AbstractUri $relativeUri
         * @return static
         */
        public function applyRelative(AbstractUri $relativeUri): self
        {
            if ($relativeUri->isQualified()) {
                if ($relativeUri->getScheme() !== '') {
                    $this->setSchemeFrom($relativeUri);
                }
                $this->setHostFrom($relativeUri);
                $this->setPortFrom($relativeUri);
                $this->setUserFrom($relativeUri);
                $this->setPassFrom($relativeUri);
            }
            $isNewPath = false;
            if ($relativeUri->isRooted()) {
                $this->setPathFrom($relativeUri);
                $isNewPath = true;
            } elseif ($relativeUri->hasPath()) {
                $this->setPath($this->getDirectory() . $relativeUri->getPath());
                $isNewPath = true;
            }
            $isNewQS = false;
            if (
                $isNewPath ||
                $relativeUri->hasQueryString()
            ) {
                $this->setQueryStringFrom($relativeUri);
                $isNewQS = true;
            }
            if (
                $isNewQS ||
                $relativeUri->hasFragment()
            ) {
                $this->setFragmentFrom($relativeUri);
            }
            return $this;
        }

        /**
         * @param AbstractUri $base
         * @return AbstractUri
         * @throws Exception
         */
        public function makeRelativeToBase(self $base): self
        {
            if (!$base->isRooted()) {
                throw new Exception('Base must be a rooted URI');
            }
            if (!$this->isRooted()) {
                throw new Exception('Can only make rooted URIs relative');
            }
            if (
                $this->getHost() !== $base->getHost() ||
                $this->getPort() !== $base->getPort() ||
                $this->getUser() !== $base->getUser() ||
                $this->getPass() !== $base->getPass()
            ) {
                if ($this->getScheme() === $base->getScheme()) {
                    $this->setScheme('');
                }
                return $this;
            }
            $this
                ->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUser(null)
                ->setPass(null);

            $baseDirTrail = $base->getDirectoryPathTrail();
            $baseDirDepth = count($baseDirTrail);
            $dirTrail = $this->getDirectoryPathTrail();
            if (
                count($this->getDirectoryPathTrail()) >= count($baseDirTrail) &&
                $baseDirTrail ===
                array_slice($dirTrail, 0, $baseDirDepth)
            ) {
                $relativePathTrail = array_slice($dirTrail, $baseDirDepth);
                $document = $this->getDocument();
                if(
                    $document &&
                    $document != $base->getDocument()
                ){
                    $relativePathTrail[] = $document;
                }

                if (!$relativePathTrail) {
                    $this->setPathTrail([]);
                    if (
                        $this->getQueryString() === $base->getQueryString()
                    ) {
                        $this->setQueryString(null);
                        if ($this->getFragment() === $base->getFragment()) {
                            $this->setFragment(null);
                        } elseif (
                            !$this->hasFragment() &&
                            $base->hasFragment()
                        ) {
                            $this->setFragment('');
                        }
                    } elseif (
                        $this->getQueryString() === null &&
                        $base->getQueryString() !== null
                    ) {
                        $this->setQueryString('');
                    }
                } else {
                    if ($this->hasTrailingSlash()) {
                        $relativePathTrail[] = '';
                    }
                    $this->setPathTrail($relativePathTrail);
                }
            } else {
                if (!$this->isRooted()) {
                    $this->setScheme('');
                }
            }
            return $this;
        }

        /**
         * @return string[]
         */
        public function getDirectoryPathTrail(): array
        {
            $trail = explode('/', $this->getDirectory());
            array_pop($trail);
            if (
                $trail &&
                !reset($trail)
            ) {
                array_shift($trail);
            }
            return $trail;
        }

        /**
         * @return string
         */
        abstract public function getDocument(): string;

        /**
         * @param string[] $relativePathTrail
         * @return static
         */
        abstract public function setPathTrail(array $relativePathTrail): self;

        /**
         * @param string $directory
         * @return static
         */
        abstract public function setDirectory(string $directory): self;

        /**
         * @param string $document
         * @return static
         */
        abstract public function setDocument(string $document): self;

        /**
         * @param string $path
         * @return static
         */
        public function descend(string $path): self
        {
            return
                $this
                    ->setPath(
                        $this->getPath() .
                        ($this->hasTrailingSlash() ? '' : '/') .
                        $path
                    )
                    ->setQueryString(null)
                    ->setFragment(null);
        }

        public function hasDocument(): bool
        {
            return ($this->getDocument() !== '');
        }

        /**
         * @param int $levels
         * @return static
         * @throws ErrorException
         */
        public function ascend(int $levels = 1): self
        {
            if ($levels < 1) {
                throw new InvalidArgumentException('Levels must be at least 1');
            }
            $trail = $this->getPathTrail();
            if ($levels > count($trail)) {
                throw new ErrorException('Cannot ascend that many levels');
            }
            return
                $this
                    ->setPath(
                        implode('/', array_slice($trail, 0, -$levels)) . '/'
                    )
                    ->setQueryString(null)
                    ->setFragment(null);
        }

        /**
         * @return int
         */
        public function getPathDepth(): int
        {
            return
                substr_count($this->getPath(), '/') -
                ($this->hasTrailingSlash() ? 1 : 0);
        }

        protected function invalidateCache()
        {
            $this->cachedUriString = null;
            $this->cachedUriStringWithoutQueryFragment = null;
        }
    }