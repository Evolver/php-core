<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

use \stdClass;
use \Exception;

class InputTests extends Tests
{
    protected function GetInputClasses()
    {
        yield Input::class;
    }

    protected function GetGoodValues()
    {
        yield 'Null' => null;
        yield 'Int' => 1;
        yield 'Float' => 1.2;
        yield 'String' => 'string';
        yield 'Boolean' => false;
    }

    protected function GetBadValues()
    {
        yield 'Object' => new stdClass;

        $fp = fopen( 'php://stdin', 'rb' );
        parent::AssertNotFalse( $fp );
        parent::AssertTrue( is_resource( $fp ) );
        try
        {
            yield 'Resource' => $fp;
        }
        finally
        {
            fclose( $fp );
        }
    }

    protected function GetNormalizationValues()
    {
        yield 'Null' => array( null, null );
        yield 'Int' => array( 1, 1 );
        yield 'Float' => array( 1.2, 1.2 );
        yield 'String' => array( 'string', 'string' );
        yield 'Boolean' => array( false, false );
    }

    protected function GetGoodValidations()
    {
        foreach( $this->GetGoodValues() as $valueName => $value )
        {
            yield [ 'No-op', $valueName ] => array( $value, null );
            yield [ 'Array', $valueName ] => array( $value, [] );
            yield [ 'Function', $valueName ] => array( $value, function() {} );
        }
    }

    protected function GetBadValidations()
    {
        foreach( $this->GetGoodValues() as $valueName => $value )
        {
            yield [ 'stdClass', $valueName ] => array( $value, new stdClass );

            try
            {
                $fp = fopen( 'php://input', 'rb' );
                Core::Assert( is_resource( $fp ) );

                yield [ 'resource', $valueName ] => array( $value, $fp );
            }
            finally
            {
                fclose( $fp );
            }

            yield [ 'String', $valueName ] => array( $value, '' );
        }
    }

    protected function GoodDefaultVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetGoodValues() as $valueName => $value )
            {
                yield [ $className, $valueName ] => array( $className, $value );
            }
        }
    }

    public function GoodDefaultVals( $inputClass, $value )
    {
        $input = new $inputClass( $undefinedVariable, $value );
        parent::AssertTrue( $input->HasDefault() );
    }

    protected function BadDefaultVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetBadValues() as $valueName => $value )
            {
                yield [ $className, $valueName ] => array( $className, $value );
            }
        }
    }

    public function BadDefaultVals( $inputClass, $value )
    {
        parent::AssertThrows( function() use( $inputClass, $value )
        {
            new $inputClass( $undefinedVariable, $value );
        });
    }

    protected function GoodInitialVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetGoodValues() as $valueName => $value )
            {
                yield [ $className, $valueName ] => array( $className, $value );
            }
        }
    }

    public function GoodInitialVals( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertFalse( $input->HasDefault() );
    }

    protected function BadInitialVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetBadValues() as $valueName => $value )
            {
                yield [ $className, $valueName ] => array( $className, $value );
            }
        }
    }

    public function BadInitialVals( $inputClass, $value )
    {
        parent::AssertThrows( function() use( $inputClass, $value )
        {
            new $inputClass( $value );
        });
    }

    protected function Normalization_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetNormalizationValues() as $valueName => $values )
            {
                array_unshift( $values, $className );

                yield [ $className, $valueName ] => $values;
            }
        }
    }

    public function Normalization( $inputClass, $initialValue, $normalizedValue )
    {
        new $inputClass( $initialValue );
        parent::AssertEq( $initialValue, $normalizedValue );
    }

    protected function Def_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            yield $className => $className;
        }
    }

    public function Def( $inputClass )
    {
        parent::AssertTrue( $inputClass::Def( $undef1, 5 ) );
        parent::AssertEq( $undef1, 5 );

        parent::AssertTrue( $inputClass::Def( $undef2, null ) );
        parent::AssertEq( $undef2, null );

        parent::AssertFalse( $inputClass::Def( $undef3, 7, 0 ) );
        parent::AssertEq( $undef3, null );

        $def1 = 10;
        parent::AssertFalse( $inputClass::Def( $def1, 11 ) );
        parent::AssertEq( $def1, 10 );

        $def2 = 12;
        parent::AssertTrue( $inputClass::Def( $def2, 13, 12 ) );
        parent::AssertEq( $def2, 13 );
    }

    protected function GoodCheckVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetGoodValidations() as $validationName => $validationInfo )
            {
                array_unshift( $validationInfo, $className );
                yield [ $className, $validationName ] => $validationInfo;
            }
        }
    }

    public function GoodCheckVals( $inputClass, $value, $validator )
    {
        parent::Context( [ & $value ], function() use( $inputClass, $validator, &$value )
        {
            $input = $inputClass::Check( $value, UNDEFINED, $validator );
            parent::AssertTrue( $input instanceof $inputClass );
        });

        parent::Context( [ & $value ], function() use( $inputClass, $validator, &$value )
        {
            $validatedValue = $inputClass::Validate( $value, UNDEFINED, $validator );
            parent::AssertTrue( $validatedValue === $value );
        });
    }

    protected function BadCheckVals_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetBadValidations() as $validationName => $validationInfo )
            {
                array_unshift( $validationInfo, $className );
                yield [ $className, $validationName ] => $validationInfo;
            }
        }
    }

    public function BadCheckVals( $inputClass, $value, $validator )
    {
        parent::Context( [ & $value ], function() use( $inputClass, $validator, &$value )
        {
            parent::AssertThrows( function() use( $inputClass, $value, $validator )
            {
                $inputClass::Check( $value, UNDEFINED, $validator );
            });
        });

        parent::Context( [ & $value ], function() use( $inputClass, $validator, &$value )
        {
            parent::AssertThrows( function() use( $inputClass, $value, $validator )
            {
                $inputClass::Validate( $value, UNDEFINED, $validator );
            });
        });
    }
}