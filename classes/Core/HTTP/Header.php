<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\HTTP;

use \Core\Core;
use \Core\MIME;

class Header
{
    const DISPOSITION_INLINE = 'inline';
    const DISPOSITION_ATTACHMENT = 'attachment';

    /**
     * Returns Status header string.
     *
     * @param int $code
     *     HTTP status code.
     *
     * @param string $string
     *     Status string.
     *
     * @param mixed $protocol
     *     Protocol to use.
     *     If null, protocol is extracted from $_SERVER[ 'SERVER_PROTOCOL' ].
     *     If string, used as protocol (form: HTTP/?.?).
     *
     * @return string
     */
    public static function Status( $code, $string, $protocol = null )
    {
        if( $protocol === null )
        {
            Core::Assert( array_key_exists( 'SERVER_PROTOCOL', $_SERVER ) );

            $protocol = $_SERVER[ 'SERVER_PROTOCOL' ];
        }

        return $protocol . ' ' . $code . ' ' . $string;
    }

    /**
     * Status 200 "OK" header.
     *
     * @see Header::Status() for more info.
     */
    public static function Status_OK( $protocol = null )
    {
        return static::Status( 200, 'OK', $protocol );
    }

    /**
     * Status 404 "Not Found" header.
     *
     * @see Header::Status() for more info.
     */
    public static function Status_NotFound( $protocol = null )
    {
        return static::Status( 404, 'Not Found', $protocol );
    }

    /**
     * Status 403 "Forbidden" header.
     *
     * @see Header::Status() for more info.
     */
    public static function Status_Forbidden( $protocol = null )
    {
        return static::Status( 403, 'Forbidden', $protocol );
    }

    /**
     * Status 500 "Internal Server Error" header.
     *
     * @see Header::Status() for more info.
     */
    public static function Status_InternalError( $protocol = null )
    {
        return static::Status( 500, 'Internal Server Error', $protocol );
    }

    /**
     * Returns Content-Type header string.
     *
     * Use of "UTF-8" as $charset is encouraged.
     *
     * @param string $mimeType
     *
     * @param mixed $charset
     *     If null, not used.
     *     If string, appended to header.
     *
     * @return
     *     string
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     * @see http://en.wikipedia.org/wiki/Internet_media_type
     * @see http://en.wikipedia.org/wiki/Character_encoding
     */
    public static function ContentType( $mimeType = MIME::XHTML, $charset = null )
    {
        $ret = 'Content-Type: ' . $mimeType;

        if( $charset !== null )
        {
            $ret .= '; charset=' . $charset;
        }

        return $ret;
    }

    /**
     * Returns Content-Disposition header string.
     *
     * @param string $type
     *
     * @param string $name
     *
     * @return string
     */
    public static function ContentDisposition( $type = self::DISPOSITION_INLINE, $name = null )
    {
        $ret = 'Content-Disposition: ' . $type;

        if( $name !== null )
        {
            $ret .= '; filename="' . $name .'"';
        }

        return $ret;
    }
}