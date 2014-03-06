<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \Core\Core;

class String extends Text
{
    /**
     * @see Input::Normalize()
     */
    protected function Normalize()
    {
        parent::Normalize();

        // Strip line feeds and caret returns at start and end of the string.
        // This way we normalize slightly malformed strings to a single-line format.
        $this->value = preg_replace( '/^[\\r\\n]*(.+)[\\r\\n]*$/u', '$1', $this->value );

        Core::Assert( is_string( $this->value ) );
        Core::Assert( parent::IsSingleLine() );
    }
}