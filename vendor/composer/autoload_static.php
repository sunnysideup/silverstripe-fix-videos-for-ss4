<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf32d74323cd34f4d9121a9de1b94fd2d
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sunnysideup\\SearchSimpleSmart\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sunnysideup\\SearchSimpleSmart\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf32d74323cd34f4d9121a9de1b94fd2d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf32d74323cd34f4d9121a9de1b94fd2d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
