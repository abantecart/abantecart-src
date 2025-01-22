<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit150cd5415d91060955f929fe7052a31c
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit150cd5415d91060955f929fe7052a31c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit150cd5415d91060955f929fe7052a31c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit150cd5415d91060955f929fe7052a31c::$classMap;

        }, null, ClassLoader::class);
    }
}
