<?php

namespace EasyCSV\Tests;

if ( ! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once __DIR__ . '/TestInit.php';

class AllTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('EasyCSV Tests');

        $suite->addTestSuite('EasyCSV\Tests\ReaderTest');
        $suite->addTestSuite('EasyCSV\Tests\WriterTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}