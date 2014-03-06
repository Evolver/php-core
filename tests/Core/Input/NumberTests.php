<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \stdClass;
use \Core\InputTests;

class NumberTests extends InputTests
{
    protected function GetInputClasses()
    {
        yield Number::class;
    }

    protected function GetGoodValues()
    {
        yield 'Null' => null;
        yield 'Int' => 1;
        yield 'Float' => 1.2;
        yield 'String 123' => '123';
        yield 'String 123.45' => '123.45';
        yield 'Boolean' => false;
    }

    protected function GetBadValues()
    {
        foreach( parent::GetBadValues() as $name => $value )
        {
            yield $name => $value;
        }

        yield 'Non-numeric string' => 'asdasd';
    }

    protected function GetNormalizationValues()
    {
        yield 'Null' => array( null, 0 );
        yield 'Int' => array( 1, 1 );
        yield 'Float' => array( 1.2, 1.2 );
        yield 'String 123' => array( '123', 123 );
        yield 'String 123.45' => array( '123.45', 123.45 );
        yield 'String 0xFF' => array( '0xFF', 255 );
        yield 'Boolean false' => array( false, 0 );
        yield 'Boolean true' => array( true, 1 );
    }

    protected function GetPositiveNumbers()
    {
        yield '1' => 1;
        yield '2.34' => 2.34;
        yield 'String 5' => '5';
        yield 'String 6.7' => '6.7';
        yield 'String 0xAB' => '0xAB';
    }

    protected function GetNegativeNumbers()
    {
        yield '-1' => -1;
        yield '-2.34' => -2.34;
        yield 'String -5' => '-5';
        yield 'String -6.7' => '-6.7';
    }

    protected function GetSignedNumbers()
    {
        return $this->GetNegativeNumbers();
    }

    protected function GetUnsignedNumbers()
    {
        yield '0' => 0;

        foreach( $this->GetPositiveNumbers() as $name => $number )
        {
            yield $name => $number;
        }
    }

    protected function GetGoodValidations()
    {
        foreach( parent::GetGoodValidations() as $name => $value )
        {
            yield $name => $value;
        }

        foreach( $this->GetPositiveNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive true' ] => [ $value, [ 'positive' => true ] ];
            yield [ $numberName, 'Array negative false' ] => [ $value, [ 'negative' => false ] ];
            yield [ $numberName, 'Array unsigned true positive' ] => [ $value, [ 'unsigned' => true ] ];
            yield [ $numberName, 'Array unsigned false positive' ] => [ $value, [ 'unsigned' => false ] ];
        }

        foreach( $this->GetNegativeNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive false' ] => [ $value, [ 'positive' => false ] ];
            yield [ $numberName, 'Array negative true' ] => [ $value, [ 'negative' => true ] ];
            yield [ $numberName, 'Array unsigned false negative' ] => [ $value, [ 'unsigned' => false ] ];
        }

        foreach( $this->GetSignedNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive false signed' ] => [ $value, [ 'positive' => false ] ];
            yield [ $numberName, 'Array unsigned false signed' ] => [ $value, [ 'unsigned' => false ] ];
            yield [ $numberName, 'Array negative true signed' ] => [ $value, [ 'negative' => true ] ];
        }

        foreach( $this->GetUnsignedNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array unsigned true unsigned' ] => [ $value, [ 'unsigned' => true ] ];
            yield [ $numberName, 'Array negative false unsigned' ] => [ $value, [ 'negative' => false ] ];
        }

        foreach( $this->GetNormalizationValues() as $numberName => $valueInfo )
        {
            list( $initial, $normalized ) = $valueInfo;

            yield [ $numberName, 'Array unsigned min lt' ] => [ $normalized, [ 'min' => ( $normalized - 1 ) ] ];
            yield [ $numberName, 'Array unsigned min lte' ] => [ $normalized, [ 'min' => $normalized ] ];
            yield [ $numberName, 'Array unsigned max gt' ] => [ $normalized, [ 'max' => ( $normalized + 1 ) ] ];
            yield [ $numberName, 'Array unsigned max gte' ] => [ $normalized, [ 'max' => $normalized ] ];
        }

        yield 'Array unsigned zero true' => [ 0, [ 'unsigned' => true ] ];
        yield 'Array unsigned zero false' => [ 0, [ 'unsigned' => false ] ];
    }

    protected function GetBadValidations()
    {
        foreach( parent::GetBadValidations() as $name => $value )
        {
            yield $name => $value;
        }

        foreach( $this->GetPositiveNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive false' ] => [ $value, [ 'positive' => false ] ];
            yield [ $numberName, 'Array negative true' ] => [ $value, [ 'negative' => true ] ];
        }

        foreach( $this->GetNegativeNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive true' ] => [ $value, [ 'positive' => true ] ];
            yield [ $numberName, 'Array negative false' ] => [ $value, [ 'negative' => false ] ];
            yield [ $numberName, 'Array unsigned true negative' ] => [ $value, [ 'unsigned' => true ] ];
        }

        foreach( $this->GetSignedNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array positive true signed' ] => [ $value, [ 'positive' => true ] ];
            yield [ $numberName, 'Array unsigned true signed' ] => [ $value, [ 'unsigned' => true ] ];
            yield [ $numberName, 'Array negative false signed' ] => [ $value, [ 'negative' => false ] ];
        }

        foreach( $this->GetUnsignedNumbers() as $numberName => $value )
        {
            yield [ $numberName, 'Array negative true unsigned' ] => [ $value, [ 'negative' => true ] ];
        }

        foreach( $this->GetNormalizationValues() as $numberName => $valueInfo )
        {
            list( $initial, $normalized ) = $valueInfo;

            yield [ $numberName, 'Array unsigned min' ] => [ $normalized, [ 'min' => ( $normalized + 1 ) ] ];
            yield [ $numberName, 'Array unsigned max' ] => [ $normalized, [ 'max' => ( $normalized - 1 ) ] ];
        }

        yield 'Array negative zero true' => [ 0, [ 'negative' => true ] ];
        yield 'Array positive zero true' => [ 0, [ 'positive' => true ] ];
    }

    protected function IsPositive_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetPositiveNumbers() as $name => $number )
            {
                yield [ $className, $name ] => array( $className, $number );
            }
        }
    }

    public function IsPositive( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertTrue( $input->IsPositive() );
    }

    protected function IsNotPositive_Fixtures()
    {
        return $this->IsNegative_Fixtures();
    }

    public function IsNotPositive( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertFalse( $input->IsPositive() );
    }

    protected function IsNegative_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetNegativeNumbers() as $name => $number )
            {
                yield [ $className, $name ] => array( $className, $number );
            }
        }
    }

    public function IsNegative( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertTrue( $input->IsNegative() );
    }

    protected function IsNotNegative_Fixtures()
    {
        return $this->IsPositive_Fixtures();
    }

    public function IsNotNegative( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertFalse( $input->IsNegative() );
    }

    protected function IsUnsigned_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetPositiveNumbers() as $name => $number )
            {
                yield [ $className, $name ] => array( $className, $number );
            }

            yield [ $className, '0' ] => array( $className, 0 );
        }
    }

    public function IsUnsigned( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertTrue( $input->IsUnsigned() );
    }

    protected function IsNotUnsigned_Fixtures()
    {
        return $this->IsNegative_Fixtures();
    }

    public function IsNotUnsigned( $inputClass, $value )
    {
        $input = new $inputClass( $value );
        parent::AssertFalse( $input->IsUnsigned() );
    }
}