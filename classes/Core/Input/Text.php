<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \Core\Core;
use \Core\Input;
use \Core\String;

class Text extends Input
{
    /**
     * @see Input::Normalize()
     */
    protected function Normalize()
    {
        parent::Normalize();

        if( is_string( $this->value ) )
        {
            // http://www.php.net/manual/en/security.filesystem.nullbytes.php
            Core::Assert( String::Pos( $this->value, "\0" ) === false );
            return;
        }

        if( is_null( $this->value ) )
        {
            $this->value ='';
        }
        else if( is_int( $this->value ) )
        {
            $this->value = strval( $this->value );
        }
        else if( is_real( $this->value ) )
        {
            $this->value = strval( $this->value );
        }
        else if( is_bool( $this->value ) )
        {
            $this->value = ( $this->value ? '1' : '0' );
        }
        else
        {
            Core::Fail( 'Unsupported type ' . gettype( $this->value ) );
        }
    }

    /**
     * Checks whether the text is a single-line text.
     *
     * @return boolean
     */
    public function IsSingleLine()
    {
        return String::SingleLine( $this->value );
    }

    /**
     * Checks whether the text is a multiple-line text.
     *
     * @return boolean
     */
    public function IsMultiLine()
    {
        return String::MultiLine( $this->value );
    }

    /**
     * Checks whether the text is shorter than $max characters.
     *
     * @param int $max
     *
     * @param boolean $orEq
     *     If true, length equality will be tested as well.
     *
     * @return boolean
     */
    public function IsShorter( $max, $orEq = false )
    {
        $length = String::Length( $this->value );

        if( $length > $max )
        {
            return false;
        }

        if( ( $length == $max ) && ( !$orEq ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the text is longer than $min characters.
     *
     * @param int $min
     *
     * @param boolean $orEq
     *     If true, length equality will be tested as well.
     *
     * @return boolean
     */
    public function IsLonger( $min, $orEq = false )
    {
        $length = String::Length( $this->value );

        if( $length < $min )
        {
            return false;
        }

        if( ( $length == $min ) && ( !$orEq ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the text is of specified $length.
     *
     * @param int $length
     *
     * @return boolean
     */
    public function HasLength( $length )
    {
        return ( String::Length( $this->value ) == $length );
    }

    /**
     * Checks whether the text matches $regex.
     *
     * @param string $regex
     *
     * @return boolean
     */
    public function IsRegexMatch( $regex )
    {
        $match = preg_match( $regex, $this->value );

        if( $match === 1 )
        {
            return true;
        }
        else if( $match === 0 )
        {
            return false;
        }
        else if( $match === false )
        {
            Core::Fail( 'Bad regular expression ' . $regex );
        }
        else
        {
            Core::Fail( 'Unexpected preg_match() return value ' . var_export( $match, true ) );
        }
    }

    /**
     * Checks whether the text has less than $max lines.
     *
     * @param int $max
     *
     * @param boolean $orEq
     *     If true, line count equality will be tested as well.
     *
     * @return boolean
     */
    public function HasLessLines( $max, $orEq = false )
    {
        $lines = String::Lines( $this->value );

        if( $lines > $max )
        {
            return false;
        }

        if( ( $lines == $max ) && ( !$orEq ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the text has more than $mix lines.
     *
     * @param int $min
     *
     * @param boolean $orEq
     *     If true, line count equality will be tested as well.
     *
     * @return boolean
     */
    public function HasMoreLines( $min, $orEq = false )
    {
        $lines = String::Lines( $this->value );

        if( $lines < $min )
        {
            return false;
        }

        if( ( $lines == $min ) && ( !$orEq ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether there are $lines lines in the text.
     *
     * @param int $lines
     *
     * @return boolean
     */
    public function HasLines( $lines )
    {
        return ( String::Lines( $this->value ) == $lines );
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
                if( array_key_exists( 'length', $validator ) )
                {
                    Core::Assert( $input->HasLength( $length = $validator[ 'length' ] ),
                                  'Not ' . $length . ' characters long' );
                }

                if( array_key_exists( 'length.min', $validator ) )
                {
                    Core::Assert( $input->IsLonger( $min = $validator[ 'length.min' ], true ),
                                  'Not at minimum ' . $min . ' characters long' );
                }

                if( array_key_exists( 'length.max', $validator ) )
                {
                    Core::Assert( $input->IsShorter( $max = $validator[ 'length.max' ], true ),
                                 'Not at maximum ' . $max . ' characters long' );
                }

                if( array_key_exists( 'lines', $validator ) )
                {
                    Core::Assert( $input->HasLines( $lines = $validator[ 'lines' ] ),
                                  'Line count is not ' . $lines );
                }

                if( array_key_exists( 'lines.min', $validator ) )
                {
                    Core::Assert( $input->HasMoreLines( $min = $validator[ 'lines.min' ], true ),
                                  'Line count is not greater or equal to ' . $min );
                }

                if( array_key_exists( 'lines.max', $validator ) )
                {
                    Core::Assert( $input->HasLessLines( $max = $validator[ 'lines.max' ], true ),
                                  'Line count is not less or equal to ' . $max );
                }

                if( array_key_exists( 'regex', $validator ) )
                {
                    Core::Assert( $input->IsRegexMatch( $regex = $validator[ 'regex' ] ),
                                  'Does not match regex ' . $regex );
                }

            }
        }

        return $input;
    }
}