<?php

namespace iutnc\deefy\loader;

class Psr4ClassLoader {

    private string $prefixes, $root;

    public function __construct(string $prefixes, string $root) {
        $this->prefixes = $prefixes;
        $this->root = $root;
    }

    public function loadClass(string $classname) {
        if (!str_starts_with($classname, $this->prefixes)) {
            return;
        }
        $relativeClass = substr($classname, strlen($this->prefixes));
        $file = $this->root . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if(is_file($file)) {
            require_once $file;
        }
    }

    public function register() {
        spl_autoload_register([$this,'loadClass']);
    }

}