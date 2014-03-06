<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

/**
 * Depending on PHP extension currently loaded, one or another implementation
 * of Core\String class is used:
 *
 *     php_mbstring    Core\String\MbString
 *     none            Core\String\Standard
 */

if( extension_loaded( 'mbstring' ) )
{
    class String extends String\MbString { };
}
else
{
    class String extends String\Standard { };
}