<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \Core\String;
use \Core\InputTests;

class TextTests extends InputTests
{
    protected function GetInputClasses()
    {
        yield Text::class;
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
        foreach( parent::GetBadValues() as $name => $value )
        {
            yield $name => $value;
        }

        yield 'Array' => array( array() );
        yield 'String with zero byte' => ( 'Dmitry' . "\0" .'Stepanov' );
    }

    protected function GetNormalizationValues()
    {
        yield 'Null' => array( null, '' );
        yield 'Int' => array( 1, '1' );
        yield 'Negative int' => array( -1, '-1' );
        yield 'Float' => array( 1.2, '1.2' );
        yield 'Negative float' => array( -1.2, '-1.2' );
        yield 'Single-line string' => array( 'string', 'string' );
        yield 'Multi-line string' => array( 'string' . "\n" . 'string',
                                            'string' . "\n" . 'string' );
        yield 'Boolean true' => array( true, '1' );
        yield 'Boolean false' => array( false, '0' );
    }

    protected function GetSingleLineTexts()
    {
        yield 'Empty' => '';
        yield 'Couple of spaces' => '   ';
        yield 'Caret return' => "\r";
        yield 'Regular string' => 'Dmitry Stepanov';
    }

    protected function GetMultiLineTexts()
    {
        yield 'Line feed' => "\n";
        yield 'Several line feeds' => "\n\n";
        yield 'Regular string, feed at end' => ( 'Dmitry Stepanov' . "\n" );
        yield 'Regular string, feed at start' => ( "\n" . 'Dmitry Stepanov' );
    }

    protected function GetGoodRegexValidations()
    {
        yield 'Empty' => [ '', '/^$/' ];
        yield 'Single line' => [ 'Dmitry Stepanov', '/^Dmitry/' ];
        yield 'Multi line' => [ 'Dmitry' . "\n" . 'Stepanov', '/^Dmitry\\n/' ];
    }

    protected function GetBadRegexValidations()
    {
        yield 'Empty' => [ '', '/.+/' ];
    }

    protected function GetGoodValidations()
    {
        foreach( parent::GetGoodValidations() as $name => $value )
        {
            yield $name => $value;
        }

        foreach( $this->GetSingleLineTexts() as $name => $value )
        {
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => 1 ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => 0 ] ];
            yield [ $name, 'lines.min eq' ] => [ $value, [ 'lines.min' => 1 ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => 2 ] ];
            yield [ $name, 'lines.max eq' ] => [ $value, [ 'lines.max' => 1 ] ];
        }

        foreach( $this->GetMultiLineTexts() as $name => $value )
        {
            $lines = String::Lines( $value );
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => $lines ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => ( $lines - 1 ) ] ];
            yield [ $name, 'lines.min eq' ] => [ $value, [ 'lines.min' => $lines ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => ( $lines + 1 ) ] ];
            yield [ $name, 'lines.max eq' ] => [ $value, [ 'lines.max' => $lines ] ];
        }

        foreach( $this->GetNormalizationValues() as $name => $valueInfo )
        {
            list( $original, $value ) = $valueInfo;
            $length = String::Length( $value );
            $lines = String::Lines( $value );

            // Lines
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => $lines ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => ( $lines - 1 ) ] ];
            yield [ $name, 'lines.min eq' ] => [ $value, [ 'lines.min' => $lines ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => ( $lines + 1 ) ] ];
            yield [ $name, 'lines.max eq' ] => [ $value, [ 'lines.max' => $lines ] ];

            // Length (characters)
            yield [ $name, 'length' ] => [ $value, [ 'length' => $length ] ];
            yield [ $name, 'length.min' ] => [ $value, [ 'length.min' => ( $length - 1 ) ] ];
            yield [ $name, 'length.min eq' ] => [ $value, [ 'length.min' => $length ] ];
            yield [ $name, 'length.max' ] => [ $value, [ 'length.max' => ( $length + 1 ) ] ];
            yield [ $name, 'length.max eq' ] => [ $value, [ 'length.max' => $length ] ];
        }

        foreach( $this->GetGoodRegexValidations() as $name => $valueInfo )
        {
            list( $value, $regex ) = $valueInfo;

            yield [ $name, 'regex' ] => [ $value, [ 'regex' => $regex ] ];
        }
    }

    protected function GetBadValidations()
    {
        foreach( parent::GetBadValidations() as $name => $value )
        {
            yield $name => $value;
        }

        foreach( $this->GetSingleLineTexts() as $name => $value )
        {
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => 0 ] ];
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => 2 ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => 2 ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => 0 ] ];
        }

        foreach( $this->GetMultiLineTexts() as $name => $value )
        {
            $lines = String::Lines( $value );
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => 1 ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => ( $lines + 1 ) ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => ( $lines - 1 ) ] ];
        }

        foreach( $this->GetNormalizationValues() as $name => $valueInfo )
        {
            list( $original, $value ) = $valueInfo;
            $length = String::Length( $value );
            $lines = String::Lines( $value );

            // Lines
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => ( $lines + 1 ) ] ];
            yield [ $name, 'lines' ] => [ $value, [ 'lines' => ( $lines - 1 ) ] ];
            yield [ $name, 'lines.min' ] => [ $value, [ 'lines.min' => ( $lines + 1 ) ] ];
            yield [ $name, 'lines.max' ] => [ $value, [ 'lines.max' => ( $lines - 1 ) ] ];

            // Length (characters)
            yield [ $name, 'length' ] => [ $value, [ 'length' => ( $length + 1 ) ] ];
            yield [ $name, 'length' ] => [ $value, [ 'length' => ( $length - 1 ) ] ];
            yield [ $name, 'length.min' ] => [ $value, [ 'length.min' => ( $length + 1 ) ] ];
            yield [ $name, 'length.max' ] => [ $value, [ 'length.max' => ( $length - 1 ) ] ];
        }

        foreach( $this->GetBadRegexValidations() as $name => $valueInfo )
        {
            list( $value, $regex ) = $valueInfo;

            yield [ $name, 'regex' ] => [ $value, [ 'regex' => $regex ] ];
        }
    }

    protected function IsSingleLine_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetSingleLineTexts() as $name => $text )
            {
                yield [ $className, $name ] => array( $className, $text );
            }
        }
    }

    public function IsSingleLine( $inputClass, $text )
    {
        $input = new $inputClass( $text );
        parent::AssertTrue( $input->IsSingleLine() );
        parent::AssertFalse( $input->IsMultiLine() );
    }

    protected function IsNotSingleLine_Fixtures()
    {
        foreach( $this->GetInputClasses() as $className )
        {
            foreach( $this->GetMultiLineTexts() as $name => $text )
            {
                yield [ $className, $name ] => array( $className, $text );
            }
        }
    }

    public function IsNotSingleLine( $inputClass, $text )
    {
        $input = new $inputClass( $text );
        parent::AssertFalse( $input->IsSingleLine() );
        parent::AssertTrue( $input->IsMultiLine() );
    }
}