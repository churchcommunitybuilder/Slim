<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2016 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Slim\Http;

/**
 * Provides a PSR-7 implementation of a reusable raw request body
 */
class RequestBody extends Body
{
    /**
     * Create a new RequestBody.
     */
    public function __construct()
    {
        $openingTempTook = 0;
        $streamCopyTook = 0;
        $rewindTook = 0;

        try {
            $start = microtime(true);
            $stream = fopen('php://temp', 'w+');
            $openingTempTook = microtime(true) - $start;
            stream_copy_to_stream(fopen('php://input', 'r'), $stream);
            $streamCopyTook = microtime(true) - $start;
            rewind($stream);
            $rewindTook = microtime(true) - $start;

            parent::__construct($stream);
        } finally {
            $stop = microtime(true);
            if ($stop - $start > 1) {
                error_log('Request Body took ' . ($stop - $start));
                error_log(print_r([
                    'openingTempTook' => $openingTempTook,
                    'streamCopyTook' => $streamCopyTook,
                    'rewindTook' => $rewindTook,
                    'total' => $stop - $start,
                ], 1));
            }
        }
    }
}
