<?php

    namespace Exteon\Uri;

    use InvalidArgumentException;

    class PhpUri extends AbstractUri
    {
        use SchemeHostTrait, PathTrailTrait, FragmentTrait;

        /** @var string|null */
        protected $fragment;

        /** @var array<string,string|array<string,string>> */
        protected $query = [];

        /** @var bool */
        protected $hasNonNullEmptyQuery;

        /**
         * URI constructor.
         * @param string|null $scheme
         * @param string|null $host
         * @param int|null $port
         * @param string|null $user
         * @param string|null $pass
         * @param string $path
         * @param string|null $fragment
         * @param array<string,string|array<string,string>> $query
         */
        public function __construct(
            ?string $scheme = null,
            ?string $host = null,
            ?int $port = null,
            ?string $user = null,
            ?string $pass = null,
            string $path = '',
            array $query = [],
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
                ->setQuery($query);
        }

        /**
         * http_build_query omits values that are NULL, so converting
         * NULL to '' here
         *
         * @param array $a
         * @return array
         */
        public static function nullValuesToEmptyString(array $a): array
        {
            return array_map(
                function ($value) {
                    if (is_array($value)) {
                        return static::nullValuesToEmptyString($value);
                    } elseif ($value === null) {
                        return '';
                    }
                    return $value;
                },
                $a
            );
        }

        /**
         * http_build_query omits values that are NULL, so filter them out
         *
         * @param array $a
         * @return array
         */
        public static function nullValuesUnset(array $a): array
        {
            $filtered = [];
            array_walk(
                $a,
                function ($value, $key) use (&$filtered) {
                    if (is_array($value)) {
                        $filtered[$key] = static::nullValuesUnset($value);
                    } elseif ($value !== null) {
                        $filtered[$key] = $value;
                    }
                }
            );
            return $filtered;
        }

        /**
         * @param array $a
         * @return bool
         */
        public static function hasNullValues(array $a): bool
        {
            foreach ($a as $value) {
                if (is_array($value)) {
                    if (static::hasNullValues($value)) {
                        return true;
                    }
                } elseif ($value === null) {
                    return true;
                }
            }
            return false;
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
            return
                ($this->query ?
                    http_build_query($this->query) :
                    ($this->hasNonNullEmptyQuery ?
                        '' :
                        null
                    )
                );
        }

        /**
         * @param string|null $queryString
         * @return static
         */
        public function setQueryString(?string $queryString): AbstractUri
        {
            if ($queryString !== null) {
                parse_str($queryString, $query);
                if (!$query) {
                    $this->hasNonNullEmptyQuery = true;
                }
            } else {
                $query = [];
                $this->hasNonNullEmptyQuery = false;
            }
            $this->query = $query;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @param string $key
         * @param $value
         * @return static
         */
        public function setQueryParameter(string $key, $value): self
        {
            $this->query[$key] = $value;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @param string $key
         * @return static
         */
        public function unsetQueryParameter(string $key): self
        {
            unset($this->query[$key]);
            $this->invalidateCache();
            return $this;
        }

        /**
         * @param string $key
         * @return bool
         */
        public function hasQueryParameter(string $key): bool
        {
            return array_key_exists($key, $this->query);
        }

        /**
         * @return string|null
         */
        public function getFragment(): ?string
        {
            return $this->fragment;
        }

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setQueryStringFrom(AbstractUri $uri): AbstractUri
        {
            if ($uri instanceof self) {
                return $this->setQuery($uri->getQuery());
            }
            return parent::setQueryStringFrom($uri);
        }

        /**
         * @return array<string,string|array<string,string>>
         */
        public function getQuery(): array
        {
            return $this->query;
        }

        /**
         * @param array<string,string|array<string,string>> $query
         * @return static
         */
        public function setQuery(array $query): self
        {
            if (static::hasNullValues($query)) {
                throw new InvalidArgumentException(
                    'Query cannot contain null values'
                );
            }
            $this->query = $query;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return bool
         */
        protected function hasQueryString(): bool
        {
            return
                !empty($this->query) ||
                $this->hasNonNullEmptyQuery;
        }
    }