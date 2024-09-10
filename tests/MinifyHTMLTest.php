<?php

declare(strict_types=1);

namespace Minify\Test;

use Minify_HTML;

class MinifyHTMLTest extends TestCase
{
    public function test1()
    {
        $src         = \file_get_contents(self::$test_files . '/html/before.html');
        $minExpected = \file_get_contents(self::$test_files . '/html/before.min.html');

        $minOutput = Minify_HTML::minify($src, [
            'cssMinifier' => ['Minify_CSSmin', 'minify'],
            'jsMinifier'  => ['JSMin\\JSMin', 'minify'],
        ]);

        $this->assertEquals($minExpected, $minOutput);
    }

    public function test2()
    {
        $src         = \file_get_contents(self::$test_files . '/html/before2.html');
        $minExpected = \file_get_contents(self::$test_files . '/html/before2.min.html');

        $minOutput = Minify_HTML::minify($src, [
            'cssMinifier' => ['Minify_CSSmin', 'minify'],
            'jsMinifier'  => ['JSMin\\JSMin', 'minify'],
        ]);

        $this->assertEquals($minExpected, $minOutput);
    }
}
