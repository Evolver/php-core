<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

class StringTests extends TextTests
{
    protected function GetInputClasses()
    {
        yield String::class;
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

        yield 'Multi-line string' => ( 'Dmitry' . "\n" . 'Stepanov' );
    }

    protected function GetNormalizationValues()
    {
        yield 'Null' => array( null, '' );
        yield 'Int' => array( 1, '1' );
        yield 'Negative int' => array( -1, '-1' );
        yield 'Float' => array( 1.2, '1.2' );
        yield 'Negative float' => array( -1.2, '-1.2' );
        yield 'String' => array( 'string', 'string' );
        yield 'String with feeds at boundaries' => array( "\r\n\n\r" . 'string' . "\n\r\r\n",
                                                          'string' );
        yield 'Boolean true' => array( true, '1' );
        yield 'Boolean false' => array( false, '0' );
    }

    protected function GetSingleLineTexts()
    {
        yield 'Empty' => '';
        yield 'Couple of spaces' => '   ';
        yield 'Caret return' => "\r";
        yield 'Regular string' => 'Dmitry Stepanov';
        yield 'Feeds at boundaries' => ( "\r\n\n\r" . 'Dmitry Stepanov' . "\r\n\n\r" );
    }

    protected function GetMultiLineTexts()
    {
        return array();
    }

    protected function GetGoodRegexValidations()
    {
        yield 'Empty' => [ '', '/^$/' ];
        yield 'Single line' => [ 'Dmitry Stepanov', '/^Dmitry/' ];

    }

    protected function GetBadRegexValidations()
    {
        yield 'Empty' => [ '', '/.+/' ];
        yield 'Multi line' => [ 'Dmitry' . "\n" . 'Stepanov', '/^Dmitry\\n/' ];
    }
}