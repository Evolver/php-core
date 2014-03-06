<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class TestsTests extends Tests
{
    protected $sequence = array();

    public function FirstSuite()
    {
        parent::AssertTrue( empty( $this->sequence ) );

        $this->sequence[] = 'First';
    }

    public function SecondSuite()
    {
        parent::AssertEq( count( $this->sequence ), 1 );
        parent::AssertTrue( array_key_exists( 0, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 0 ], 'First' );

        $this->sequence[] = 'Second';
    }

    public function ThirdSuite()
    {
        parent::AssertEq( count( $this->sequence ), 2 );
        parent::AssertTrue( array_key_exists( 0, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 0 ], 'First' );
        parent::AssertTrue( array_key_exists( 1, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 1 ], 'Second' );

        $this->sequence[] = 'Third';
    }

    public function EnsureSuiteSequence()
    {
        parent::AssertEq( count( $this->sequence ), 3 );
        parent::AssertTrue( array_key_exists( 0, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 0 ], 'First' );
        parent::AssertTrue( array_key_exists( 1, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 1 ], 'Second' );
        parent::AssertTrue( array_key_exists( 2, $this->sequence ) );
        parent::AssertEq( $this->sequence[ 2 ], 'Third' );
    }

    public function Assertions()
    {
        parent::AssertTrue( true );
        parent::AssertTrue( 1, false );
        parent::AssertNotTrue( false );
        parent::AssertNotTrue( 1 );
        parent::AssertFalse( false );
        parent::AssertFalse( 0, false );
        parent::AssertNotFalse( true );
        parent::AssertNotFalse( 0 );
        parent::AssertNotFalse( 1, false );
        parent::AssertNull( null );
        parent::AssertNull( 0, false );
        parent::AssertNotNull( 0 );
        parent::AssertNotNull( 1, false );
        parent::AssertEq( false, false );
        parent::AssertEq( false, '0', false );
        parent::AssertNeq( false, true );
        parent::AssertNeq( false, '0' );
        parent::AssertNeq( false, '1', false );
        parent::AssertLess( 1, 2 );
        parent::AssertGreater( 2, 1 );

        parent::AssertThrows( function()
        {
            Core::Fail( 'Synthetic error' );
        });
        parent::AssertNoThrow( function()
        {
            // No-op
        });

        parent::AssertThrows( function()
        {
            parent::AssertEq( false, '0' );
        });

        parent::AssertThrows( function()
        {
            parent::AssertLess( 2, 2 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertLess( 2, 1 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertGreater( 2, 2 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertGreater( 1, 2 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertTrue( 1 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotTrue( true );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotTrue( 1, false );
        });

        parent::AssertThrows( function()
        {
            parent::AssertFalse( 0 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotFalse( false );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotFalse( 0, false );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNull( 0 );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotNull( null );
        });

        parent::AssertThrows( function()
        {
            parent::AssertNotNull( 0, false );
        });

        parent::AssertNoOutput( function()
        {
        });

        parent::AssertThrows( function()
        {
            parent::AssertNoOutput( function()
            {
                echo 'Output data';
            });
        });

        parent::AssertOutputEq( '', function()
        {

        });

        parent::AssertOutputEq( '123', function()
        {
            echo '123';
        });

        parent::AssertThrows( function()
        {
            parent::AssertOutputEq( '123', function()
            {
                echo 'Output data';
            });
        });
    }

    public function Contexts()
    {
        $localVar = 0;
        $localArr = array( 'x' => 1, 'y' => 2, 'z' => 3);

        parent::Context( [ & $localVar, & $localArr ],
                         function() use( &$localVar, &$localArr )
        {
            parent::AssertEq( $localVar, 0 );
            ++$localVar;
            parent::AssertEq( $localVar, 1 );

            parent::AssertEq( count( $localArr ), 3 );
            parent::AssertTrue( array_key_exists( 'x', $localArr ) );
            parent::AssertTrue( array_key_exists( 'y', $localArr ) );
            parent::AssertTrue( array_key_exists( 'z', $localArr ) );
            parent::AssertEq( $localArr[ 'x' ], 1 );
            parent::AssertEq( $localArr[ 'y' ], 2 );
            parent::AssertEq( $localArr[ 'z' ], 3 );

            unset( $localArr[ 'x' ] );
            $localArr[ 'y' ] = 10;
            $localArr[ 'z' ] += 1;

            parent::AssertFalse( array_key_exists( 'x', $localArr ) );
            parent::AssertEq( $localArr[ 'y' ], 10 );
            parent::AssertEq( $localArr[ 'z' ], 4 );
        });

        parent::AssertEq( $localVar, 0 );
        parent::AssertEq( count( $localArr ), 3 );
        parent::AssertTrue( array_key_exists( 'x', $localArr ) );
        parent::AssertTrue( array_key_exists( 'y', $localArr ) );
        parent::AssertTrue( array_key_exists( 'z', $localArr ) );
        parent::AssertEq( $localArr[ 'x' ], 1 );
        parent::AssertEq( $localArr[ 'y' ], 2 );
        parent::AssertEq( $localArr[ 'z' ], 3 );
    }

    public function BadTests()
    {
        parent::AssertThrows( function()
        {
            $this->ExecuteNested( _Dummy_BadTest::class );
        });
    }

    public static function ShouldNotBeCalled()
    {
        Core::Fail( 'Should not call static functions' );
    }

    protected function ShouldNotBeCalled2()
    {
        Core::Fail( 'Should not call protected functions' );
    }

    public function Fixtures()
    {
        parent::AssertThrows( function()
        {
            $this->ExecuteNested( _Dummy_Not_Fixture::class );
        });

        $this->ExecuteNested( _Dummy_Fixture::class );
    }
}

class _Dummy_BadTest extends Tests
{
    public function ShouldNeverPass()
    {
        return 'someValue';
    }
}

class _Dummy_Fixture extends Tests
{
    protected $sum;

    protected function Test_Fixtures()
    {
        $this->sum = 0;

        yield 1;
        yield 2;
        yield 3;
    }

    public function Test( $value )
    {
        $this->sum += $value;
    }

    public function Verify()
    {
        parent::AssertEq( $this->sum, 6 );
    }

    protected function Test2_Fixtures()
    {
        $this->sum = 0;

        yield array( 1, 10 );
        yield array( 2, 20 );
        yield array( 3, 30 );
    }

    public function Test2( $val1, $val2 )
    {
        parent::AssertLess( $val1, $val2 );

        $this->sum += ( $val1 + $val2 );
    }

    public function Verify2()
    {
        parent::AssertEq( $this->sum, 66 );
    }
}

class _Dummy_Not_Fixture extends Tests
{
    // This function should not be treated as fixture because it
    // is public. All public functions are test cases which should
    // never return a value. In this case this function will return
    // a generator object and test executor should catch this as
    // an error.
    public function ShouldNotBeCalled3_Fixture()
    {
        yield 'test';
    }
}