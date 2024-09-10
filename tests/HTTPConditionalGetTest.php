<?php

declare(strict_types=1);

namespace Minify\Test;

use HTTP_ConditionalGet;

class HTTPConditionalGetTest extends TestCase
{
    public static function TestData()
    {
        /*
         * NOTE: calculate $lmTime as parameter
         * because dataProviders are executed before tests run,
         * and in fact each test is new instance of the class, so can't share data
         * other than parameters.
         */

        $lmTime  = \time() - 900;
        $gmtTime = \gmdate('D, d M Y H:i:s \G\M\T', $lmTime);

        return [
            [
                'lm'   => $lmTime,
                'desc' => 'client has valid If-Modified-Since',
                'inm'  => null,
                'ims'  => $gmtTime,
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime}\"",
                    'Cache-Control' => 'max-age=0, private',
                    '_responseCode' => 'HTTP/1.0 304 Not Modified',
                    'isValid'       => true,
                ]
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'client has valid If-Modified-Since with trailing semicolon',
                'inm'  => null,
                'ims'  => $gmtTime . ';',
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime}\"",
                    'Cache-Control' => 'max-age=0, private',
                    '_responseCode' => 'HTTP/1.0 304 Not Modified',
                    'isValid'       => true,
                ],
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'client has valid ETag (non-encoded version)',
                'inm'  => "\"badEtagFoo\", \"pri{$lmTime}\"",
                'ims'  => null,
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime}\"",
                    'Cache-Control' => 'max-age=0, private',
                    '_responseCode' => 'HTTP/1.0 304 Not Modified',
                    'isValid'       => true,
                ],
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'client has valid ETag (gzip version)',
                'inm'  => "\"badEtagFoo\", \"pri{$lmTime};gz\"",
                'ims'  => null,
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime};gz\"",
                    'Cache-Control' => 'max-age=0, private',
                    '_responseCode' => 'HTTP/1.0 304 Not Modified',
                    'isValid'       => true,
                ],
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'no conditional get',
                'inm'  => null,
                'ims'  => null,
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime};gz\"",
                    'Cache-Control' => 'max-age=0, private',
                    'isValid'       => false,
                ],
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'client has invalid ETag',
                'inm'  => '"pri' . ($lmTime - 300) . '"',
                'ims'  => null,
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime};gz\"",
                    'Cache-Control' => 'max-age=0, private',
                    'isValid'       => false,
                ],
            ],
            [
                'lm'   => $lmTime,
                'desc' => 'client has invalid If-Modified-Since',
                'inm'  => null,
                'ims'  => gmdate('D, d M Y H:i:s \G\M\T', $lmTime - 300),
                'exp'  => [
                    'Vary'          => 'Accept-Encoding',
                    'Last-Modified' => $gmtTime,
                    'ETag'          => "\"pri{$lmTime};gz\"",
                    'Cache-Control' => 'max-age=0, private',
                    'isValid'       => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider TestData
     */
    public function test_HTTP_ConditionalGet($lm, $desc, $inm, $ims, $exp)
    {
        // setup env
        if (null === $inm) {
            unset($_SERVER['HTTP_IF_NONE_MATCH']);
        } else {
            $_SERVER['HTTP_IF_NONE_MATCH'] = $inm;
        }

        if (null === $ims) {
            unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        } else {
            $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $ims;
        }

        $cg             = new HTTP_ConditionalGet([
            'lastModifiedTime' => $lm,
            'encoding'         => 'x-gzip',
        ]);
        $ret            = $cg->getHeaders();
        $ret['isValid'] = $cg->cacheIsValid;

        $this->assertEquals($exp, $ret, $desc);
    }
}
