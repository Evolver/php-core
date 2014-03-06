<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

class Real extends Number
{
    /**
     * @see Input::Normalize()
     */
    protected function Normalize()
    {
        parent::Normalize();

        if( is_real( $this->value ) )
        {
            return;
        }

        if( is_int( $this->value ) )
        {
            $this->value = floatval( $this->value );
        }
        else
        {
            Core::Fail( 'Unsupported type ' . gettype( $this->value ) );
        }

        // FIXME: implement rounding
    }
}