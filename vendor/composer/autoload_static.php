<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbb60eea2b502f7708ab659d5342be586
{
    public static $files = array (
        'bc687bc59848e05fe06304fc9a56325a' => __DIR__ . '/../..' . '/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Container\\' => 14,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'C' => 
        array (
            'Core\\' => 5,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Core',
        ),
        'App\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitbb60eea2b502f7708ab659d5342be586::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbb60eea2b502f7708ab659d5342be586::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbb60eea2b502f7708ab659d5342be586::$classMap;

        }, null, ClassLoader::class);
    }
}
