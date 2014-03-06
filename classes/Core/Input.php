<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

use \Closure;

/**
 * Checks whether certain data adheres to a certain format / type.
 * Additionally, may normalize the data to meet the requested format / type.
 */
class Input
{
    /**
     * Value that is being analyzed (ref).
     * If === $uninitValue, then not initialized.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Value that $value should have in order for it to be
     * considered as uninitialized.
     *
     * @var mixed
     */
    protected $uninitValue;

    /**
     * Default value to initialize $value with.
     * If === UNDEFINED, then was not specified.
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Constructor.
     *
     * If $value is not initialized, it will receive $default
     * $default value (including in the calling scope).
     *
     * @param mixed $value
     *     Variable to analyze the value of (by ref).
     *
     * @param mixed $default
     *     Default value to assign in case $value is not defined.
     *
     * @param mixed $uninit
     *     A value that $value should be equal to in order for it to
     *     be considered as uninitialized. Makes sense only if $default
     *     was specified.
     */
    public function __construct( &$value,
                                 $default = UNDEFINED,
                                 $uninit = null )
    {
        $this->value = &$value;
        $this->uninitValue = $uninit;

        $hasDefault = false;

        if( $value === $uninit )
        {
            if( $default !== UNDEFINED )
            {
                $value = $default;
                $hasDefault = true;
            }
        }

        if( !$hasDefault )
        {
            if( $value === $default )
            {
                $hasDefault = true;
            }
        }

        $this->Normalize();

        if( $hasDefault )
        {
            // Receives normalized default value
            $this->defaultValue = $this->value;
        }
        else
        {
            // Receives original default value
            $this->defaultValue = $default;
        }
    }

    /**
     * Checks whether the value is still equal to default value.
     *
     * @return boolean
     */
    public final function HasDefault()
    {
        if( $this->defaultValue === UNDEFINED )
        {
            return false;
        }

        return ( $this->value == $this->defaultValue );
    }

    /**
     * Invoked when normalization of the value is required.
     * The goal of normalization is to bring $value to a common format / data type.
     *
     * It is expected that child class will overload this function and add
     * custom logic.
     *
     * @throws
     *     Normalization could not be performed.
     */
    protected function Normalize()
    {
        Core::Assert( !is_object( $this->value ) );
        Core::Assert( !is_resource( $this->value ) );
    }

    /**
     * Assigns $default to $value if $value is uninitialized (=== $uninit).
     *
     * @param mixed $value
     *
     * @param mixed $default
     *
     * @param mixed $uninit
     *
     * @return
     *     If $default has been assigned to $value, returns true. Returns false
     *     otherwise.
     */
    public static function Def( &$value, $default, $uninit = null )
    {
        if( $value === $uninit )
        {
            $value = $default;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Executes $validator against the specified $value. If throws an exception, then
     * validation has failed.
     *
     * Before validation takes place, checks whether $value is initialized (i.e. is not null).
     * If not initialized, initializes it with $default. If $value has been initialized with
     * $default, no validations will take place. Default values are considered to always be safe.
     *
     * @param mixed $value
     *
     * @param mixed $default
     *
     * @param mixed $validator
     *     If null, then no validations are performed.
     *     If is an array, then validations enlisted in the array will take place.
     *     If instanceof Closure, then $validator( $value ) will be executed with $this set to the instance of Input.
     *     Otherwise, throws an exception.
     *
     * @return
     *     Input instance allocated to perform the validation.
     */
    public static function Check( &$value, $default = UNDEFINED, $validator = null )
    {
        $input = new static( $value, $default );

        if( !$input->HasDefault() )
        {
            if( $validator === null )
            {
                // No-op
            }
            else if( is_array( $validator ) )
            {
                // No-op. Child classes are expected to add custom validations.
            }
            else if( $validator instanceof Closure )
            {
                $validator = $validator->bindTo( $input );
                $validator( $value );
            }
            else
            {
                Core::Fail( 'Unsupported $validator type ' . gettype( $validator ) );
            }
        }

        return $input;
    }

    /**
     * Similar to Check(), returns ref to $value instead.
     *
     * @see Input::Check()
     */
    public static function & Validate( &$value, $default = UNDEFINED, $validator = null )
    {
        static::Check( $value, $default, $validator );
        return $value;
    }
}
