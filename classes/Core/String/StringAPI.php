<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\String;

use \Core\Core;

/**
 * String manipulation class. An abstraction layer between userland and
 * the underlying UTF-8 implementation.
 *
 * All strings given to this class shall be UTF-8 encoded if not explicitly
 * stated otherwise.
 */
class StringAPI
{
    /**
     * Returns count of characters in $string.
     *
     * @param string $string
     *
     * @return int
     *
     * @throws
     *     Length could not be determined.
     */
    public static function Length( $string )
    {
        Core::Fail( 'Not supported' );
    }

    /**
     * Looks for first occurence of $needle in $haystack starting the search at $offset.
     *
     * @param string $haystack
     *
     * @param string $needle
     *
     * @param int $offset
     *
     * @return
     *     If $needle found, returns int.
     *     Otherwise, returns false.
     *
     * @throws
     *     Could not perform $string scan.
     */
    public static function Pos( $string, $needle, $offset = 0 )
    {
        Core::Fail( 'Not supported' );
    }

    /**
     * Converts $string to upper case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function UpperCase( $string )
    {
        Core::Fail( 'Not supported' );
    }

    /**
     * Converts $string to lower case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function LowerCase( $string )
    {
        Core::Fail( 'Not supported' );
    }

    /**
     * Checks whether $string is a single-line string.
     *
     * @param string $string
     *
     * @param string $delimiter
     *
     * @return boolean
     */
    public static function SingleLine( $string, $delimiter = "\n" )
    {
        return ( static::Pos( $string, $delimiter ) === false );
    }

    /**
     * Checks whether $string is a multiple-line string.
     *
     * @param string $string
     *
     * @param string $delimiter
     *
     * @return boolean
     */
    public static function MultiLine( $string, $delimiter = "\n" )
    {
        return ( static::Pos( $string, $delimiter ) !== false );
    }

    /**
     * Returns count of lines in $string.
     *
     * @param string $string
     *
     * @param string $delimiter
     *     Line delimiting string.
     *
     * @return int
     */
    public static function Lines( $string, $delimiter = "\n" )
    {
        $lines = 1;
        $offset = 0;

        while( true )
        {
            $offset = static::Pos( $string, $delimiter, $offset );

            if( $offset === false )
            {
                break;
            }

            ++$lines;
            ++$offset;
        }

        return $lines;
    }
}