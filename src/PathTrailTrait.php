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
            if ($path) {
                $pathTrail = explode('/', $path);
            } else {
                $pathTrail = [];
            }
            return $this->setPathTrail($pathTrail);
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
        public function getDirectoryPathTrail(): array
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
            $this->invalidateCache();
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
            $pathTrail = $this->getDirectoryPathTrail();
            $pathTrail[] = $document;
            $this->pathTrail = $pathTrail;
            $this->invalidateCache();;
            return $this;
        }

        public function getPathTrail(): array
        {
            return $this->pathTrail;
        }

        /**
         * @param array $pathTrail
         * @return static
         */
        public function setPathTrail(array $pathTrail): AbstractUri
        {
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
                $this->hasTrailingSlash = true;
                if(!$pathTrail){
                    $this->isPathRooted = true;
                }
            } else {
                $this->hasTrailingSlash = false;
            }
            $this->pathTrail = $pathTrail;
            $this->invalidateCache();;
            return $this;
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
            $this->hasTrailingSlash = (bool)$this->pathTrail ||
                $this->isPathRooted;
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
            $this->invalidateCache();
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

        public function hasTrailingSlash(): bool
        {
            return $this->hasTrailingSlash;
        }
    }