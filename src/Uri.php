<?php

    namespace Exteon\Uri;

    class Uri extends AbstractUri
    {
        /**  @var string | null */
        protected $queryString;

        /**
         * Uri constructor.
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
            $this->setScheme( $scheme);
            $this->setHost ($host);
            $this->setPort ( $port);
            $this->setUser ( $user);
            $this->setPass ( $pass);
            $this->setPath($path);
            $this->setFragment ( $fragment);
            $this->setQueryString($queryString);
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
         */
        public function setQueryString(?string $queryString): void {
            $this->queryString = $queryString;
        }

        /**
         * @return bool
         */
        protected function hasQueryString(): bool
        {
            return ($this->queryString !== null);
        }

        /**
         * @param AbstractUri $from
         */
        protected function setQueryStringFrom(AbstractUri $from): void
        {
            $this->setQueryString($from->getQueryString());
        }
    }