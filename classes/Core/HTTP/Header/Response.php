<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\HTTP\Header;

use \Core\Core;
use \Core\MIME;
use \Core\HTTP\Header;

/**
 * Same as Header, except the header strings are passed
 * to header() function.
 */
class Response extends Header
{
    /**
     * @see Header::Status()
     *
     * @return
     *     void
     *
     * Sends header string via header() instead of returning it.
     */
    public static function Status( $code, $string, $protocol = null )
    {
        header( parent::Status( $code, $string, $protocol ) );
    }

    /**
     * @see Header::ContentType()
     *
     * @return
     *     void
     *
     * Sends header string via header() instead of returning it.
     */
    public static function ContentType( $mimeType = MIME::XHTML, $charset = null )
    {
        header( parent::ContentType( $mimeType, $charset ) );
    }

    /**
     * @see Header::ContentDisposition()
     *
     * @return
     *     void
     *
     * Sends header string via header() instead of returning it.
     */
    public static function ContentDisposition( $type = self::DISPOSITION_INLINE, $name = null )
    {
        header( parent::ContentDisposition( $type, $name ) );
    }
}