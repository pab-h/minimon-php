<?php

    namespace Pab;

    require_once __DIR__.'/vendor/autoload.php';
    require_once __DIR__.'/src/Minimalizer.php';

    final class Minimon {
        private array $srcs = array();
        private array $dists = array();
        private \CliArgs\CliArgs $args;

        public function __construct() {
            $this->args = $this->setupArgs();
            $this->prepareArgs();
            $this->minimalizer = new \Pab\Core\Minimalizer();
            $this->startWatch();
        }

        public static function formatDir(string $dirname): string {
            return str_replace("/", '\\', __DIR__."/$dirname");
        }

        private function setupArgs(): \CliArgs\CliArgs {
            return new \CliArgs\CliArgs(
                array(
                    'src' => array(
                        'alias' => 's',
                        'help' => 'Directory that will be watched'
                    ),
                    'dist' => array(
                        'alias' => 'd',
                        'help' => 'Directory that will receive the minimized files'
                    )
                )
            );
        }

        private function prepareArrayArgs(string $key): array {
            $array = $this->args->getArg($key);
            $array = explode(',', $array);
            $array = array_map('trim', $array);

            return $array;
        }

        private function prepareArgs(): void {

            $srcs = $this->prepareArrayArgs('src');
            $srcs = array_map('self::formatDir', $srcs);
            $this->srcs = $srcs;

            $dists = $this->prepareArrayArgs('dist');
            $dists = array_map('self::formatDir', $dists);
            $this->dists = $dists;

            if(count($this->srcs) !== count($this->dists)) {
                throw new \Error("The amount of 'src' does not match the amount of 'dists'", 400);
            }

        }

        private function getDistByPath(string $path): string {
            $index = array_search(pathinfo($path, PATHINFO_DIRNAME), $this->srcs);

            return $this->dists[$index];
        }

        private function startWatch(): void {
            \Spatie\Watcher\Watch::paths($this->srcs)
                ->onFileUpdated(function(string $path) {
                    $this->minimalizer->setup(
                        $path, 
                        $this->getDistByPath($path)
                    );
                    $this->minimalizer->minify();
                })
                ->start();
        }
    }

    new Minimon();

?>