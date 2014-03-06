<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

use \Exception;
use \ReflectionClass;
use \ReflectionMethod;

use Tests\Observer;

/**
 * Unit tests shall inherit from this class and define test
 * cases as public methods. Instances of this class are called
 * "test suites" and individual public methods "test cases".
 *
 * Test suites are meant to be executed under the supervision
 * of Tests\Observer instance. Current state of test suite
 * (the test case being executed and successes/failures) are
 * reported to the Observer. See Tests\Observer class for
 * more details.
 */
abstract class Tests
{
    /**
     * Count of assertions executed so far.
     *
     * @var uint
     */
    private $assertions = 0;

    /**
     * Count of contexts used so far.
     *
     * @var uint
     */
    private $contexts = 0;

    /**
     * Test execution observer object.
     * Initialized when test instance is created.
     * This object receives statistics on test execution status.
     *
     * @var Observer
     */
    private $observer;

    /**
     * Suffix used to identify fixture functions.
     *
     * @var string
     */
    private static $fixturesSuffix = '_Fixtures';

    /**
     * Constructor.
     *
     * @param Observer $observer
     */
    public function __construct( $observer )
    {
        $this->observer = $observer;
    }

    /**
     * Returns iterator of all test methods.
     *
     * Yields test method name as a key and an array with the following keys:
     *     method            - ReflectionMethod of a test method;
     *     fixtureMethod     - ReflectionMethod of a method that generates
     *                         fixtures for the test. If no fixture method is
     *                         defined for the test, contains null.
     */
    public static function GetTests()
    {
        $info = new ReflectionClass( static::class );

        $testCaseMethods = $info->getMethods(
            ReflectionMethod::IS_PUBLIC
        );

        foreach( $testCaseMethods as $method )
        {
            /* @var $method ReflectionMethod */
            $testName = $method->GetName();

            // Skip methods inherited from this class
            if( $method->getDeclaringClass()->getName() === self::class )
            {
                continue;
            }

            if( $method->isStatic() )
            {
                continue;
            }

            // Skip magic methods
            if( substr( $testName, 0, 2 ) == '__' )
            {
                continue;
            }

            // If fixture method is defined, invoke it and obtain
            // an iteratable object from where to extract the fixtures.
            try
            {
                $fixturesMethod = $info->getMethod( $testName . self::$fixturesSuffix );
                Core::Assert( $fixturesMethod->isProtected() );
            }
            catch( Exception $e )
            {
                $fixturesMethod = null;
            }

            yield $testName => array(
                'method' => $method,
                'fixturesMethod' => $fixturesMethod
            );
        }
    }

    /**
     * Executes test cases defined in the child class.
     *
     * Test cases are public methods that have not been inherited
     * from this class.
     *
     * If a fixture method for a test case is present (i.e. a
     * protected function with "_Fixture" suffix), it is executed
     * to obtain an iteratable object to use as a fixture set.
     *
     * @return
     *     Observer
     */
    public final function Execute()
    {
        $this->observer->OnStart( $this );

        try
        {
            foreach( $this->GetTests() as $testName => $methodInfo )
            {
                $method = $methodInfo[ 'method' ];
                $fixturesMethod = $methodInfo[ 'fixturesMethod' ];

                if( $fixturesMethod !== null )
                {
                    $fixturesMethod->setAccessible( true );
                    $fixtures = $fixturesMethod->invoke( $this );
                }
                else
                {
                    $fixtures = null;
                }

                if( $fixtures === null )
                {
                    // Execute without fixture
                    $continue = $this->ExecuteTest( $method );
                }
                else
                {
                    // Execute with fixtures
                    foreach( $fixtures as $key => $value )
                    {
                        try
                        {
                            $continue = $this->ExecuteTest( $method, $key, $value );
                        }
                        catch( Exception $e )
                        {
                            Core::Fail( 'Test failed', 0, $e );
                        }

                        if( !$continue )
                        {
                            break;
                        }
                    }
                }

                if( !$continue )
                {
                    break;
                }
            }
        }
        finally
        {
            $this->observer->OnEnd();
        }

        return $this->observer;
    }

    /**
     * Executes test suite $className using the same observer instance
     * that was used to initialize current test suite.
     *
     * @param string $className
     *     Test suite to execute.
     *
     * @return
     *     Whatever $className::Execute() will return.
     */
    public final function ExecuteNested( $className )
    {
        Core::Assert( is_subclass_of( $className, self::class ) );

        $tests = new $className( $this->observer );
        return $tests->Execute();
    }

    /**
     * Executes a single test method $method. $args is an argument list
     * to pass to $method.
     *
     * @param ReflectionMethod $method
     *
     * @param mixed $fixtureKey
     *
     * @param mixed $fixtureValue
     *
     * @return
     *     If no more consecutive tests should be executed, returns false.
     *     Otherwise, returns true.
     */
    private function ExecuteTest( $method, $fixtureKey = null, $fixtureValue = null )
    {
        $this->observer->OnTestStart( $method->getName(), $fixtureKey );

        try
        {
            if( !is_array( $fixtureValue ) )
            {
                $ret = $method->invoke( $this, $fixtureValue );
            }
            else
            {
                $ret = $method->invokeArgs( $this, $fixtureValue );
            }

            if( $ret === null )
            {
                $this->observer->OnTestPass();
            }
            else if( $ret === false )
            {
                $this->observer->OnTestSkip();
            }
            else
            {
                Core::Fail( 'Test function produced unexpected return value' );
            }

        }
        catch( Exception $e )
        {
            return $this->observer->OnTestFail( $e );
        }
        finally
        {
            $this->observer->OnTestEnd();
        }

        return true;
    }

    /**
     * Checks whether equality assertion is true.
     *
     * @param mixed $a
     *
     * @param mixed $b
     *
     * @param bool $strict
     *     If true, === is used.
     *     If false, == is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertEq( $a, $b, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $a === $b );
        }
        else
        {
            $result = ( $a == $b );
        }

        Core::Assert( $result, var_export( $a, true ) . ' is not equal to ' . var_export( $b, true ) );
    }

    /**
     * Checks whether inequality assertion is true.
     *
     * @param mixed $a
     *
     * @param mixed $b
     *
     * @param bool $strict
     *     If true, !== is used.
     *     If false, != is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNeq( $a, $b, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $a !== $b );
        }
        else
        {
            $result = ( $a != $b );
        }

        Core::Assert( $result, var_export( $a, true ) . ' is same as ' . var_export( $b, true ) );
    }

    /**
     * Checks whether lesser assertion is true.
     *
     * @param mixed $a
     *
     * @param mixed $b
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertLess( $a, $b )
    {
        $this->observer->OnAssert();

        Core::Assert( $a < $b, var_export( $a, true ) . ' is not less than ' . var_export( $b, true ) );
    }

    /**
     * Checks whether greater assertion is true.
     *
     * @param mixed $a
     *
     * @param mixed $b
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertGreater( $a, $b )
    {
        $this->observer->OnAssert();

        Core::Assert( $a > $b, var_export( $a, true ) . ' is not less than ' . var_export( $b, true ) );
    }

    /**
     * Checks whether throw assertion is true.
     *
     * @param callable $callback
     *
     * @param string $class
     *     Expected exception's class.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertThrows( $callback, $class = Exception::class )
    {
        $this->observer->OnAssert();

        try
        {
            $callback();
        }
        catch( Exception $e )
        {
            Core::Assert( $e instanceof $class, 'Thrown ' . get_class( $e ) . ' instead of ' . $class );
            return;
        }

        Core::Fail( 'Did not throw' );
    }

    /**
     * Checks whether nothrow assertion is true.
     *
     * @param callable $callback
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNoThrow( $callback )
    {
        $this->observer->OnAssert();

        try
        {
            $callback();
        }
        catch( Exception $e )
        {
            Core::Fail( 'Thrown exception while expected to not to', 0, $e );
        }
    }

    /**
     * Checks whether assertion of a $value being equal to true is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, === is used.
     *     If false, == is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertTrue( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value === true );
        }
        else
        {
            $result = ( $value == true );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is not true' );
    }

    /**
     * Checks whether assertion of a $value not being equal to true is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, !== is used.
     *     If false, != is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNotTrue( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value !== true );
        }
        else
        {
            $result = ( $value != true );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is true' );
    }

    /**
     * Checks whether assertion of a $value being equal to false is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, === is used.
     *     If false, == is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertFalse( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value === false );
        }
        else
        {
            $result = ( $value == false );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is not false' );
    }

    /**
     * Checks whether assertion of a $value not being equal to false is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, !== is used.
     *     If false, != is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNotFalse( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value !== false );
        }
        else
        {
            $result = ( $value != false );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is false' );
    }

    /**
     * Checks whether assertion of a $value being equal to null is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, === is used.
     *     If false, == is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNull( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value === null );
        }
        else
        {
            $result = ( $value == null );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is not null' );
    }

    /**
     * Checks whether assertion of a $value not being equal to null is true.
     *
     * @param mixed $value
     *
     * @param bool $strict
     *     If true, !== is used.
     *     If false, != is used.
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNotNull( $value, $strict = true )
    {
        $this->observer->OnAssert();

        if( $strict )
        {
            $result = ( $value !== null );
        }
        else
        {
            $result = ( $value != null );
        }

        Core::Assert( $result, var_export( $value, true ) . ' is null' );
    }

    /**
     * Checks whether assertion of $callback not producing any output is true.
     *
     * @param mixed $callback
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertNoOutput( $callback )
    {
        $this->observer->OnAssert();

        ob_start();
        try
        {
            $callback();
            $output = ob_get_contents();
        }
        finally
        {
            ob_end_clean();
        }

        Core::Assert( $output == '', 'Function generates output (' . strlen( $output ) .' bytes)' );
    }

    /**
     * Checks whether assertion of $callback producing $output is true.
     *
     * @param mixed $callback
     *
     * @param string $output
     *
     * @throws
     *     Assertion failed.
     */
    protected final function AssertOutputEq( $output, $callback )
    {
        $this->observer->OnAssert();

        ob_start();
        try
        {
            $callback();
            $generatedOutput = ob_get_contents();
        }
        finally
        {
            ob_end_clean();
        }

        Core::Assert( $generatedOutput == $output,
                      'Function generates output (' . strlen( $generatedOutput ) .' bytes) that differs from the ' .
                      'expected (' . strlen( $output ) .' bytes)' );
    }

    /**
     * Invokes callback before saving the state of all variables referenced in
     * $context. Once callback is done, the state of context variables is
     * restored.
     *
     * @param array $context
     *
     * @param callable $callback
     */
    protected final function Context( $context = array(), $callback )
    {
        $backup = array();

        // Save state
        foreach( $context as $key => $value )
        {
            $backup[ $key ] = $value;
        }

        try
        {
            $callback();
        }
        finally
        {
            //  Restore state
            foreach( $context as $key => &$value )
            {
                $value = $backup[ $key ];
            }
            unset( $value );
        }
    }

    /**
     * Returns associated Observer object.
     *
     * @return
     *     Observer
     */
    public final function GetObserver()
    {
        return $this->observer;
    }
}