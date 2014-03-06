<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class XML
{
    /**
     * Prepares $value to be embedded in an XML attribute in raw form.
     * Specifically, escapes all XML entities.
     *
     * @param string $value
     *
     * @return string
     */
    public static function EscapeAttr( $value )
    {
        return htmlspecialchars( $value, ENT_QUOTES | ENT_XML1, 'UTF-8' );
    }

    /**
     * Escapes special characters in $value as if it would be a content
     * inside an element.
     *
     * @param string $value
     *
     * @return string
     */
    public static function EscapeContent( $value )
    {
        return htmlspecialchars( $value, ENT_NOQUOTES | ENT_XML1, 'UTF-8' );
    }

    /**
     * Encloses specified $value in CDATA section(s).
     *
     * @param string $value
     *
     * @return string
     */
    public static function EscapeCData( $value )
    {
        $sections = explode( ']]>', $value );
        Core::Assert( !empty( $sections ) );

        if( count( $sections ) == 1 )
        {
            $glue = '';
        }
        else
        {
            $glue = ']]]]><![CDATA[>';
        }

        return '<![CDATA[' . implode( $glue, $sections ) . ']]>';
    }
}