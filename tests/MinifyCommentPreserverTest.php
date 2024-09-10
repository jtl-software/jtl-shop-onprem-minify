<?php

declare(strict_types=1);

namespace Minify\Test;

use Minify_CommentPreserver;

class MinifyCommentPreserverTest extends TestCase
{
    public function test()
    {
        $inOut = [
            '/*!*/'        => "\n/*!*/\n",
            '/*!*/a'       => "\n/*!*/\n1A",
            'a/*!*//*!*/b' => "2A\n/*!*/\n\n/*!*/\n3B",
            'a/*!*/b/*!*/' => "4A\n/*!*/\n5B\n/*!*/\n",
        ];

        $processor = [__CLASS__, '_test_MCP_processor'];
        foreach ($inOut as $in => $expected) {
            $actual = Minify_CommentPreserver::process($in, $processor);
            $this->assertSame($expected, $actual, 'Minify_CommentPreserver');
        }
    }

    /**
     * @internal
     */
    public static function _test_MCP_processor($content, $options = [])
    {
        static $callCount = 0;
        ++$callCount;
        return $callCount . \strtoupper($content);
    }
}
