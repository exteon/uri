<?php

    namespace Exteon\Uri;

    class Uri extends AbstractUri
    {
        use SchemeHostTrait, PathTrailTrait, FragmentTrait;

        /**  @var string | null */
        protected $queryString;

        /**
         * URI constructor.
         * @param string|null $scheme
         * @param string|null $host
         * @param int|null $port
         * @param string|null $user
         * @param string|null $pass
         * @param string $path
         * @param string|null $queryString
         * @param string|null $fragment
         */
        public function __construct(
            ?string $scheme = null,
            ?string $host = null,
            ?int $port = null,
            ?string $user = null,
            ?string $pass = null,
            string $path = '',
            ?string $queryString = null,
            ?string $fragment = null
        ) {
            $this
                ->setScheme($scheme)
                ->setHost($host)
                ->setPort($port)
                ->setUser($user)
                ->setPass($pass)
                ->setPath($path)
                ->setFragment($fragment)
                ->setQueryString($queryString);
        }

        public static function isTrailingSlashInsensitive(): bool
        {
            return false;
        }

        /**
         * @return string|null
         */
        public function getQueryString(): ?string
        {
            return $this->queryString;
        }

        /**
         * @param string|null $queryString
         * @return static
         */
        public function setQueryString(?string $queryString): AbstractUri
        {
            $this->queryString = $queryString;
            $this->invalidateCache();
            return $this;
        }

        public function hasQueryString(): bool
        {
            return ($this->queryString !== null);
        }
    }