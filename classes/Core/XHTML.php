<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class XHTML extends XML
{
    /**
     * Generates random string that can be used as element id.
     *
     * The following characters may appear in the generated string:
     *     a-zA-Z0-9_-.
     *
     * @return string
     */
    public static function RandomId()
    {
        static $offset = null;

        if( $offset === null )
        {
            $offset = mt_rand( 10000, 99999 );
        }

        return 'id' . ( ++$offset ) . 'rnd';
    }
}