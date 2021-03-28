<?php

    namespace Exteon\Uri;

    use ErrorException;
    use InvalidArgumentException;

    trait PathTrailTrait
    {
        /** @var string[] */
        protected $pathTrail;

        /** @var bool */
        protected $hasTrailingSlash;

        /** @var bool */
        protected $isPathRooted;

        /**s
         * @param string $path
         * @return static
         */
        public function setPath(string $path): AbstractUri
        {
            $pathTrail = explode('/', $path);
            return $this->setPathTrailAndTrailingSlashAndRooted($pathTrail);
        }

        /**
         * @param array $pathTrail
         * @return static
         */
        protected function setPathTrailAndTrailingSlashAndRooted(
            array $pathTrail
        ): self {
            if (
                count($pathTrail) > 1 &&
                !reset($pathTrail)
            ) {
                array_shift($pathTrail);
                $this->isPathRooted = true;
            } else {
                $this->isPathRooted = false;
            }
            return $this->setPathTrailAndTrailingSlash($pathTrail);
        }

        /**
         * @param array $pathTrail
         * @return static
         */
        protected function setPathTrailAndTrailingSlash(array $pathTrail): self
        {
            if (
                $pathTrail &&
                !end($pathTrail)
            ) {
                array_pop($pathTrail);
                $this->hasTrailingSlash = (bool)$pathTrail;
            } else {
                $this->hasTrailingSlash = false;
            }
            $this->pathTrail = $pathTrail;
            return $this;
        }


        /**
         * @return string
         */
        public function getPath(): string
        {
            return
                ($this->isPathRooted ? '/' : '') .
                implode('/', $this->pathTrail) .
                ($this->pathTrail && $this->hasTrailingSlash ?
                    '/' :
                    ''
                );
        }

        /**
         * Gets the document directory, including the trailing /
         *
         * @return string
         */
        public function getDirectory(): string
        {
            $directoryPathTrail = $this->getDirectoryPathTrail();
            return
                ($this->isPathRooted ? '/' : '') .
                implode('/', $directoryPathTrail) .
                ($directoryPathTrail ? '/' : '');
        }

        /**
         * @return string[]
         */
        protected function getDirectoryPathTrail(): array
        {
            if ($this->hasTrailingSlash) {
                return $this->pathTrail;
            }
            if (count($this->pathTrail) > 1) {
                return array_slice($this->pathTrail, 0, -1);
            }
            return [];
        }

        /**
         * @param string $directory
         * @return static
         */
        public function setDirectory(string $directory): AbstractUri
        {
            $document = $this->getDocument();
            $pathTrail = explode('/', $directory);
            if (!end($pathTrail)) {
                throw new InvalidArgumentException(
                    'Directory must end in \'/\''
                );
            }
            array_pop($pathTrail);
            if ($document) {
                $pathTrail[] = $document;
            }
            $this->pathTrail = $pathTrail;
            return $this;
        }

        /**
         * @return string
         */
        public function getDocument(): string
        {
            return
                $this->hasDocument() ?
                    end($this->pathTrail) :
                    '';
        }

        public function hasDocument(): bool
        {
            return
                $this->pathTrail &&
                !$this->hasTrailingSlash;
        }

        /**
         * @param string $document
         * @return static
         */
        public function setDocument(string $document): AbstractUri
        {
            if (
                $this->pathTrail &&
                !$this->hasTrailingSlash
            ) {
                array_pop($this->pathTrail);
            }
            $this->pathTrail[] = $document;
            return $this;
        }

        public function getPathTrail(): array
        {
            return $this->pathTrail;
        }

        public function getPathDepth(): int
        {
            return count($this->pathTrail);
        }

        /**
         * @param string $path
         * @return static
         */
        public function descend(string $path): AbstractUri
        {
            $pathTrail = array_merge(
                $this->getDirectoryPathTrail(),
                explode('/', $path)
            );
            return
                $this
                    ->setPathTrailAndTrailingSlash($pathTrail)
                    ->setQueryString(null)
                    ->setFragment(null);
        }

        /**
         * @param int $levels
         * @return static
         * @throws ErrorException
         */
        public function ascend(int $levels = 1): AbstractUri
        {
            if ($levels < 1) {
                throw new InvalidArgumentException('Levels must be at least 1');
            }
            if ($levels > count($this->pathTrail)) {
                throw new ErrorException('Cannot ascend that many levels');
            }
            $this->pathTrail = array_slice($this->pathTrail, 0, -$levels);
            $this->hasTrailingSlash = (bool)$this->pathTrail;
            return
                $this
                    ->setQueryString(null)
                    ->setFragment(null);
        }

        /**
         * @param AbstractUri $uri
         * @return static
         */
        public function setPathFrom(AbstractUri $uri): AbstractUri
        {
            $this->pathTrail = $uri->getPathTrail();
            $this->hasTrailingSlash = $uri->hasTrailingSlash();
            $this->isPathRooted = $uri->isPathRooted();
            return $this;
        }

        public function hasDirectory(): bool
        {
            return (
                $this->hasTrailingSlash ||
                $this->isPathRooted ||
                count($this->pathTrail) > 1
            );
        }

        public function hasPath(): bool
        {
            return (
                $this->hasTrailingSlash ||
                $this->isPathRooted ||
                $this->pathTrail
            );
        }

        public function isPathRooted(): bool
        {
            return $this->isPathRooted;
        }
    }