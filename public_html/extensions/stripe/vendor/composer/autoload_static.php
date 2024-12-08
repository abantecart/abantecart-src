<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit648d46fc039b8efa03df480951130810
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit648d46fc039b8efa03df480951130810::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit648d46fc039b8efa03df480951130810::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit648d46fc039b8efa03df480951130810::$classMap;

        }, null, ClassLoader::class);
    }
}
