<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\String;

class Standard extends StringAPI
{
    /**
     * @see StringAPI::Length()
     */
    public static function Length( $string )
    {
        return strlen( $string );
    }

    /**
     * @see StringAPI::Pos()
     */
    public static function Pos( $string, $needle, $offset = 0 )
    {
        return strpos( $string, $needle, $offset );
    }

    /**
     * @see StringAPI::UpperCase()
     */
    public static function UpperCase( $string )
    {
        return strtoupper( $string );
    }

    /**
     * @see StringAPI::LowerCase()
     */
    public static function LowerCase( $string )
    {
        return strtolower( $string );
    }
}