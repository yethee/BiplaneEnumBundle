<?php

$vendorDir = __DIR__ . '/../vendor';
require_once $vendorDir . '/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'  => $vendorDir . '/symfony/src',
    'Doctrine' => $vendorDir . '/doctrine-common/lib',
    'Metadata' => $vendorDir . '/metadata/src',
    'JMS'      => $vendorDir,
));
$loader->register();

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Biplane\\EnumBundle\\')) {
        $path = __DIR__ . '/../' . implode('/', array_slice(explode('\\', $class), 2)).'.php';
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;

        return true;
    }
});