<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\Tests;

use \Core\Core;
use \Core\Tests;
use \Exception;

class Observer
{
    /**
     * Stack of currently active test suites.
     *
     * @var array
     */
    protected $suiteStack = array();

    /**
     * Currently active test suite.
     *
     * @var Tests
     */
    protected $suite;

    /**
     * Stack of currently active test names.
     *
     * @var array
     */
    protected $testNameStack = array();

    /**
     * Name of currently active test.
     *
     * @var string
     */
    protected $testName;

    /**
     * Count of test suites executed so far.
     *
     * @var uint
     */
    public $suites = 0;

    /**
     * Count of test cases executed so far.
     *
     * @var uint
     */
    public $tests = 0;

    /**
     * Count of tests passed so far.
     *
     * @var uint
     */
    public $passed = 0;

    /**
     * Count of tests failed so far.
     *
     * @var uint
     */
    public $failed = 0;

    /**
     * Count of tests skipped so far.
     *
     * @var uint
     */
    public $skipped = 0;

    /**
     * Count of assertions so far.
     *
     * @var uint
     */
    public $assertions = 0;

    /**
     * Executed before test suite starts.
     *
     * @param Tests $suite
     */
    public function OnStart( $suite )
    {
        $this->suiteStack[] = $this->suite;
        $this->suite = $suite;

        ++$this->suites;
    }

    /**
     * Executed before test starts.
     *
     * @param string $testName
     *
     * @param mixed $fixtureKey
     *     If null, no fixture is being used.
     *     Otherwise, the key of the fixture value.
     */
    public function OnTestStart( $testName, $fixtureKey = null )
    {
        $this->testNameStack[] = $this->testName;
        $this->testName = $testName;

        ++$this->tests;
    }

    /**
     * Executed before assertion is checked.
     */
    public function OnAssert()
    {
        ++$this->assertions;
    }

    /**
     * Executed when test has been passed.
     */
    public function OnTestPass()
    {
        ++$this->passed;
    }

    /**
     * Executed when test has thrown an exception.
     *
     * @param Exception $exception
     *
     * @return
     *     If consecutive tests should be interrupted, return false.
     *     Otherwise, return true.
     */
    public function OnTestFail( $exception )
    {
        ++$this->failed;
    }

    /**
     * Executed when test has been skipped.
     */
    public function OnTestSkip()
    {
        ++$this->skipped;
    }

    /**
     * Executed when test has completed.
     */
    public function OnTestEnd()
    {
        $this->testName = array_pop( $this->testNameStack );
    }

    /**
     * Executed when test suite has completed.
     */
    public function OnEnd()
    {
        $this->suite = array_pop( $this->suiteStack );
    }

    /**
     * Executes one or more test suites passed in $tests.
     *
     * @param mixed $tests
     *     If string, will be treated as a suite (class) name to instantiate.
     *     If object, will be treated as a suite to run.
     *     If array, will be treated as list of suites to execute.
     */
    public function Execute( $tests )
    {
        if( is_array( $tests ) )
        {
            foreach( $tests as $suite )
            {
                $this->Execute( $suite );
            }
        }
        else if( is_string( $tests ) )
        {
            $obj = new $tests( $this );
            $obj->Execute();
        }
        else
        {
            Core::Fail( 'Unsupported $tests type ' . get_type( $tests ) );
        }
    }
}