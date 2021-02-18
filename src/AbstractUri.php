<?php

    namespace Exteon\Uri;

    use Exception;
    use InvalidArgumentException;

    abstract class AbstractUri
    {
        /** @var string|null */
        protected $scheme;

        /** @var string|null */
        protected $host;

        /** @var int|null */
        protected $port;

        /** @var string|null */
        protected $user;

        /** @var string|null */
        protected $pass;

        /** @var string|null */
        protected $fragment;

        /** @var string The document directory, including trailing / */
        protected $directory = '';

        /**  @var string */
        protected $document = '';

        abstract public function __construct();

        /**
         * @return bool
         */
        abstract protected function hasQueryString(): bool;

        /**
         * @return string|null
         */
        abstract public function getQueryString(): ?string;

        /**
         * @param string|null $queryString
         */
        abstract public function setQueryString(?string $queryString): void;

        /**
         * @param AbstractUri $from
         * @return mixed
         */
        abstract protected function setQueryStringFrom(self $from): void;

        /**
         * @return string|null
         */
        public function getScheme(): ?string
        {
            return $this->scheme;
        }

        /**
         * @return string|null
         */
        public function getHost(): ?string
        {
            return $this->host;
        }

        /**
         * @return int|null
         */
        public function getPort(): ?int
        {
            return $this->port;
        }

        /**
         * @return string|null
         */
        public function getUser(): ?string
        {
            return $this->user;
        }

        /**
         * @return string|null
         */
        public function getPass(): ?string
        {
            return $this->pass;
        }

        /**
         * @return string
         */
        public function getPath(): string
        {
            return $this->getDirectory() . $this->getDocument();
        }

        /**
         * @return string|null
         */
        public function getFragment(): ?string
        {
            return $this->fragment;
        }

        /**
         * @return string
         */
        public function toString(): string
        {
            $uri = '';
            if ($this->getScheme() !== null) {
                $uri .= $this->getScheme() . ':';
            }
            if ($this->getHost()) {
                $uri .= '//';
                if ($this->getUser()) {
                    $uri .= rawurlencode($this->getUser());
                    if ($this->getPass()) {
                        $uri .= ':' . rawurlencode($this->getPass());
                    }
                    $uri .= '@';
                }
                $uri .= rawurlencode($this->getHost());
                if (rawurlencode($this->getPort())) {
                    $uri .= ':' . rawurlencode($this->getPort());
                }
                if (
                    is_string($this->getPath()) &&
                    strlen($this->getPath()) &&
                    $this->getPath()[0] !== '/'
                ) {
                    $uri .= '/';
                }
            }
            if ($this->getPath()) {
                $uri .= implode(
                    '/',
                    array_map(
                        function ($component) {
                            return rawurlencode($component);
                        },
                        explode('/', $this->getPath())
                    )
                );
            }
            $qs = $this->getQueryString();
            if ($qs !== null) {
                $uri .= '?' . $qs;
            }
            if ($this->getFragment() !== null) {
                $uri .= '#' . $this->getFragment();
            }
            return $uri;
        }

        /**
         * @return bool
         */
        public function isQualified(): bool
        {
            return (
                $this->getHost() !== null ||
                $this->getScheme() !== null
            );
        }

        /**
         * @return bool
         */
        public function isRooted(): bool
        {
            return (
                $this->isQualified() ||
                substr($this->getDirectory(), 0, 1) === '/'
            );
        }

        /**
         * @param self $base
         * @throws Exception
         */
        public function composeWithBase(self $base): void
        {
            if (!$base->isRooted()) {
                throw new Exception('Base must be a rooted URI');
            }
            $wasRooted = $this->isRooted();
            $wasQualified = $this->isQualified();
            if ($this->getScheme()) {
                return;
            }
            if (
                $this->getScheme() === '' ||
                (
                    $this->getScheme() === null &&
                    !$this->getHost()
                )
            ) {
                $this->setScheme($base->getScheme());
            }
            if ($wasQualified) {
                return;
            }
            if (
                !$wasRooted &&
                !$this->getPath()
            ) {
                if (!$this->hasQueryString()) {
                    $this->setQueryStringFrom($base);
                    if ($this->getFragment() === null) {
                        $this->setFragment($base->getFragment());
                    }
                }
            }
            if (!$wasRooted) {
                $this->setPath($base->getDirectory() . $this->getPath());
            }
            $this->setHost($base->getHost());
            $this->setPort($base->getPort());
            $this->setUser($base->getUser());
            $this->setPass($base->getPass());
        }

        /**
         * @param AbstractUri $base
         * @throws Exception
         */
        public function makeRelativeToBase(self $base): void
        {
            if (!$base->isRooted()) {
                throw new Exception('Base must be a rooted URI');
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
                return;
            }
            $this->setScheme(null);
            $this->setHost(null);
            $this->setPort(null);
            $this->setUser(null);
            $this->setPass(null);
            $baseDir = $base->getDirectory();
            $thisDir = $this->getDirectory();
            $baseDirLen = strlen($baseDir);
            if (substr($thisDir, 0, $baseDirLen) === $baseDir) {
                $dir = substr($thisDir, $baseDirLen);
                $this->setDirectory($dir);
                if (!$dir) {
                    if($this->getDocument() === $base->getDocument()){
                        $this->setDocument('');
                        if ($this->getQueryString() === $base->getQueryString()) {
                            $this->setQueryString(null);
                            if ($this->getFragment() === $base->getFragment()) {
                                $this->setFragment(null);
                            } elseif (
                                $this->getFragment() === null &&
                                $base->getFragment() !== null
                            ) {
                                $this->setFragment('');
                            }
                        } elseif (
                            $this->getQueryString() === null &&
                            $base->getQueryString() !== null
                        ) {
                            $this->setQueryString('');
                        }
                    }
                }
            } else {
                if (!$this->isRooted()) {
                    $this->setScheme('');
                }
            }
        }

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
            $result->setScheme(
                $parsed['scheme'] ?? ($setEmptyScheme ? '' : null)
            );
            $result->setHost(
                isset($parsed['host']) ? rawurldecode(
                    $parsed['host']
                ) : null
            );
            $result->setPort(
                isset($parsed['port']) ? (int)$parsed['port'] : null
            );
            $result->setUser(
                isset($parsed['user']) ? rawurldecode(
                    $parsed['user']
                ) : null
            );
            $result->setPass(
                isset($parsed['pass']) ? rawurldecode(
                    $parsed['pass']
                ) : null
            );
            $result->setPath(
                isset($parsed['path']) ? rawurldecode($parsed['path']) : ''
            );
            $result->setFragment($parsed['fragment'] ?? null);
            $result->setQueryString($parsed['query'] ?? null);
            return $result;
        }

        /**
         * @param string|null $scheme
         */
        public function setScheme(
            ?string $scheme
        ): void {
            $this->scheme = $scheme;
        }

        /**
         * @param string|null $host
         */
        public function setHost(
            ?string $host
        ): void {
            $this->host = $host;
        }

        /**
         * @param int|null $port
         */
        public function setPort(
            ?int $port
        ): void {
            $this->port = $port;
        }

        /**
         * @param string|null $user
         */
        public function setUser(
            ?string $user
        ): void {
            $this->user = $user;
        }

        /**
         * @param string|null $pass
         */
        public function setPass(
            ?string $pass
        ): void {
            $this->pass = $pass;
        }

        /**
         * @param string $path
         */
        public function setPath(
            string $path
        ): void {
            preg_match('`(.*/)?([^/]*)`', $path, $match);
            $this->directory = $match[1];
            $this->document = $match[2];
        }

        /**
         * @param string|null $fragment
         */
        public function setFragment(
            ?string $fragment
        ): void {
            $this->fragment = $fragment;
        }

        /**
         * Gets the document directory, including the trailing /
         *
         * @return string
         */
        public function getDirectory(): string
        {
            return $this->directory;
        }

        /**
         * @return string
         */
        public function getDocument(): string
        {
            return $this->document;
        }

        /**
         * @param string $directory
         */
        public function setDirectory(string $directory): void
        {
            $this->directory = $directory;
        }

        /**
         * @param string $document
         */
        public function setDocument(string $document): void
        {
            $this->document = $document;
        }
    }