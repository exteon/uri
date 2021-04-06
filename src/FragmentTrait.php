<?php

    namespace Exteon\Uri;

    trait FragmentTrait
    {
        /** @var string|null */
        protected $fragment;

        /**
         * @return string|null
         */
        public function getFragment(): ?string
        {
            return $this->fragment;
        }

        /**
         * @param string|null $fragment
         * @return static
         */
        public function setFragment(?string $fragment): AbstractUri
        {
            $this->fragment = $fragment;
            $this->invalidateCache();
            return $this;
        }

        public function hasFragment(): bool
        {
            return ($this->fragment !== null);
        }

    }