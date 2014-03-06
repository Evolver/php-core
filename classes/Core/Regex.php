<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class Regex
{
    /**
     * Regular expression to match a single path component.
     *
     * @var string
     */
    public static $pathComponent = '[a-zA-Z0-9_\\-]+';

    /**
     * Regular expression to match several path components.
     *
     * Initialized below.
     *
     * @var string
     */
    public static $pathComponents;

    /**
     * Regular expression to match file/path extension.
     *
     * @var string
     */
    public static $pathExt = '[a-zA-Z\\-]+';

    /**
     * Tests whether $string matches $regex.
     *
     * @param string $regex
     *
     * @param string $string
     *
     * @param array $matches
     *
     * @param int $flags
     *
     * @param int $offset
     *
     * @return boolean
     *
     * @see http://www.php.net/manual/en/function.preg-match.php
     */
    public static function Match( $regex, $string, &$matches = null, $flags = 0, $offset = 0 )
    {
        $result = preg_match( $regex, $string, $matches, $flags, $offset );
        Core::Assert( $result !== false );

        if( $result === 0 )
        {
            return false;
        }
        else if( $result === 1 )
        {
            return true;
        }
        else
        {
            Core::Fail( 'Unexpected return value ' . var_export( $result, true ) . ' from preg_match()' );
        }
    }
}

Regex::$pathComponents = '(?:' . Regex::$pathComponent . '\\/)*' . Regex::$pathComponent;
