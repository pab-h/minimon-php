<?php 
    namespace Pab\Core;

    final class Minimalizer{

        private \MatthiasMullie\Minify\Minify $minifier;
        private string $file;

        public static function createFileMinName(string $file): string {
            return pathinfo($file, PATHINFO_FILENAME) 
                . '.min.'
                . pathinfo($file, PATHINFO_EXTENSION);
        }

        public function setup(string $file, string $dist): void {
            $this->file = $file;
            $this->dist = $dist ."\\"
                .self::createFileMinName($this->file);

            $className = strtoupper(pathinfo(
                $this->file, 
                PATHINFO_EXTENSION
            ));

            if (class_exists("\MatthiasMullie\Minify\\$className")) {
                $class = "\MatthiasMullie\Minify\\$className";

                $this->minifier = new $class();
            }
        }

        public function minify(): void {
            $this->minifier->add($this->file);
            $this->minifier->minify($this->dist);
        }

    }
?>