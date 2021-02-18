<?php

    namespace Exteon\Uri;

    use InvalidArgumentException;

    class PHPUri extends AbstractUri
    {
        /** @var array<string,string|array<string,string>> */
        protected $query = [];

        /**
         * Uri constructor.
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
            $this->setScheme($scheme);
            $this->setHost($host);
            $this->setPort($port);
            $this->setUser($user);
            $this->setPass($pass);
            $this->setPath($path);
            $this->setFragment($fragment);
            $this->setQuery($query);
        }

        /**
         * @return string|null
         */
        public function getQueryString(): ?string
        {
            return $this->query ? http_build_query($this->query) : null;
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
         */
        public function setQuery(array $query): void
        {
            if(static::hasNullValues($query)){
                throw new InvalidArgumentException('Query cannot contain null values');
            }
            $this->query = $query;
        }

        /**
         * @param string|null $queryString
         */
        public function setQueryString(?string $queryString): void
        {
            if ($queryString !== null) {
                parse_str($queryString, $query);
            } else {
                $query = [];
            }
            $this->query = $query;
        }

        /**
         * @return bool
         */
        protected function hasQueryString(): bool
        {
            return !empty($this->query);
        }

        /**
         * @param string $key
         * @param $value
         */
        public function setQueryParameter(string $key, $value): void
        {
            $this->query[$key] = $value;
        }

        /**
         * @param string $key
         */
        public function unsetQueryParameter(string $key): void
        {
            unset($this->query[$key]);
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
         * @param AbstractUri $from
         */
        protected function setQueryStringFrom(AbstractUri $from): void
        {
            if ($from instanceof static) {
                $this->setQuery($from->getQuery());
            } else {
                $this->setQueryString($from->getQueryString());
            }
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
            foreach($a as $value){
                if (is_array($value)) {
                    if(static::hasNullValues($value)){
                        return true;
                    }
                } elseif ($value === null) {
                    return true;
                }
            }
            return false;
        }
    }