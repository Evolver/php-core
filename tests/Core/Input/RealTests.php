<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Input;

use \stdClass;

class RealTests extends NumberTests
{
    protected function GetInputClasses()
    {
        yield Real::class;
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

    protected function GetNormalizationValues()
    {
        yield 'Null' => array( null, 0.0 );
        yield 'Int' => array( 1, 1.0 );
        yield 'Float' => array( 1.2, 1.2 );
        yield 'String 123' => array( '123', 123.0 );
        yield 'String 123.45' => array( '123.45', 123.45 );
        yield 'String 0xFF' => array( '0xFF', 255.0 );
        yield 'Boolean false' => array( false, 0.0 );
        yield 'Boolean true' => array( true, 1.0 );
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
}