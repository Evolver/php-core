<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\String;

class MbString extends StringAPI
{
    /**
     * @see StringAPI::Length()
     */
    public static function Length( $string )
    {
        return mb_strlen( $string, 'UTF-8' );
    }

    /**
     * @see StringAPI::Pos()
     */
    public static function Pos( $string, $needle, $offset = 0 )
    {
        return mb_strpos( $string, $needle, $offset, 'UTF-8' );
    }

    /**
     * @see StringAPI::UpperCase()
     */
    public static function UpperCase( $string )
    {
        return mb_strtoupper( $string, 'UTF-8' );
    }

    /**
     * @see StringAPI::LowerCase()
     */
    public static function LowerCase( $string )
    {
        return mb_strtolower( $string, 'UTF-8' );
    }
}