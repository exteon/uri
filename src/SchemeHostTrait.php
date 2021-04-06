<?php

    namespace Exteon\Uri;

    trait SchemeHostTrait
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

        /**
         * @return string|null
         */
        public function getScheme(): ?string
        {
            return $this->scheme;
        }

        /**
         * @param string|null $scheme
         * @return static
         */
        public function setScheme(
            ?string $scheme
        ): AbstractUri {
            if ($scheme) {
                static::validateScheme($scheme);
            }
            $this->scheme = $scheme;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return string|null
         */
        public function getHost(): ?string
        {
            return $this->host;
        }

        /**
         * @param string|null $host
         * @return static
         */
        public function setHost(
            ?string $host
        ): AbstractUri {
            $this->host = $host;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return int|null
         */
        public function getPort(): ?int
        {
            return $this->port;
        }

        /**
         * @param int|null $port
         * @return static
         */
        public function setPort(
            ?int $port
        ): AbstractUri {
            $this->port = $port;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return string|null
         */
        public function getUser(): ?string
        {
            return $this->user;
        }

        /**
         * @param string|null $user
         * @return static
         */
        public function setUser(
            ?string $user
        ): AbstractUri {
            $this->user = $user;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return string|null
         */
        public function getPass(): ?string
        {
            return $this->pass;
        }

        /**
         * @param string|null $pass
         * @return static
         */
        public function setPass(?string $pass): AbstractUri
        {
            $this->pass = $pass;
            $this->invalidateCache();
            return $this;
        }

        /**
         * @return bool
         */
        public function hasScheme(): bool
        {
            return ($this->scheme !== null);
        }

        public function hasHost(): bool
        {
            return ($this->host !== null);
        }

        public function hasUser(): bool
        {
            return ($this->user !== null);
        }

        public function hasPass(): bool
        {
            return ($this->pass !== null);
        }

        public function hasPort(): bool
        {
            return ($this->port !== null);
        }
    }