<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9273a75785ca0f649c70fb14676b4e3f
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9273a75785ca0f649c70fb14676b4e3f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9273a75785ca0f649c70fb14676b4e3f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}