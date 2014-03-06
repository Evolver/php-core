<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \Core\Core;
use \Core\Input;

class Number extends Input
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
            return;
        }

        if( is_null( $this->value ) )
        {
            $this->value = 0;
        }
        else if( is_bool( $this->value ) )
        {
            $this->value = ( $this->value ? 1 : 0 );
        }
        else if( is_string( $this->value ) )
        {
            Core::Assert( is_numeric( $this->value ) );
            $this->value += 0;
        }
        else
        {
            Core::Fail( 'Unsupported type ' . gettype( $this->value ) );
        }
    }

    /**
     * Checks whether is a positive number.
     *
     * @returns boolean
     */
    public function IsPositive()
    {
        return ( $this->value > 0 );
    }

    /**
     * Checks whether is a negative number.
     *
     * @returns boolean
     */
    public function IsNegative()
    {
        return ( $this->value < 0 );
    }

    /**
     * Checks whether the number is unsigned.
     *
     * @return boolean
     */
    public function IsUnsigned()
    {
        return ( $this->value >= 0 );
    }

    /**
     * Checks whether the number is less than $min.
     *
     * @param mixed $min
     *
     * @param boolean $orEqual
     *
     * @return boolean
     */
    public function IsLess( $min, $orEqual = false )
    {
        if( $this->value > $min )
        {
            return false;
        }

        if( ( $this->value == $min ) && ( !$orEqual ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the number is greater than $max.
     *
     * @param mixed $max
     *
     * @param boolean $orEqual
     *
     * @return boolean
     */
    public function IsGreater( $max, $orEqual = false )
    {
        if( $this->value < $max )
        {
            return false;
        }

        if( ( $this->value == $max ) && ( !$orEqual ) )
        {
            return false;
        }

        return true;
    }

    /**
     * @see Input::Check()
     */
    public static function Check( &$value, $default = UNDEFINED, $validator = null )
    {
        $input = parent::Check( $value, $default, $validator );

        if( !$input->HasDefault() )
        {
            if( is_array( $validator ) )
            {
                if( array_key_exists( 'min', $validator ) )
                {
                    Core::Assert( $input->IsGreater( $validator[ 'min' ], true ) );
                }

                if( array_key_exists( 'max', $validator ) )
                {
                    Core::Assert( $input->IsLess( $validator[ 'max' ], true ) );
                }

                if( array_key_exists( 'positive', $validator ) )
                {
                    Core::Assert( $input->IsPositive() === $validator[ 'positive' ] );
                }

                if( array_key_exists( 'negative', $validator ) )
                {
                    Core::Assert( $input->IsNegative() === $validator[ 'negative' ] );
                }

                if( array_key_exists( 'unsigned', $validator ) && $validator[ 'unsigned' ] )
                {
                    Core::Assert( $input->IsUnsigned() );
                }
            }
        }

        return $input;
    }
}