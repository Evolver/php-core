<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

class Integer extends Number
{
    /**
     * @see Input::Normalize()
     */
    protected function Normalize()
    {
        parent::Normalize();

        if( is_int( $this->value ) )
        {
            return;
        }

        if( is_real( $this->value ) )
        {
            $this->value = intval( $this->value );
        }
        else
        {
            Core::Fail( 'Unsupported type ' . gettype( $this->value ) );
        }
    }
}