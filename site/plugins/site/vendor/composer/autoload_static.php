<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit251c52173bcd743666d78f6721f2b30a
{
    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpDocumentor\\Reflection\\' => 25,
        ),
        'W' => 
        array (
            'Webmozart\\Assert\\' => 17,
            'Wagnerwagner\\Site\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpDocumentor\\Reflection\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpdocumentor/reflection-common/src',
            1 => __DIR__ . '/..' . '/phpdocumentor/type-resolver/src',
            2 => __DIR__ . '/..' . '/phpdocumentor/reflection-docblock/src',
        ),
        'Webmozart\\Assert\\' => 
        array (
            0 => __DIR__ . '/..' . '/webmozart/assert/src',
        ),
        'Wagnerwagner\\Site\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit251c52173bcd743666d78f6721f2b30a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit251c52173bcd743666d78f6721f2b30a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit251c52173bcd743666d78f6721f2b30a::$classMap;

        }, null, ClassLoader::class);
    }
}
