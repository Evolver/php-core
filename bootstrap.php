<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

use \Core\Core;

/**
 * Holds random-like value that is assigned to variables that should be
 * considered "undefined". Used when use of "null" is not applicable.
 *
 * Re-generated on every request so that it is hard to guess it.
 *
 * @var string
 */
define( 'UNDEFINED', '__undef_' . microtime( true ) . mt_rand( 10000, 99999 ) );

require_once( __DIR__ . '/classes/Core/Core.php' );

Core::AddNamespace( 'Core', __DIR__ . '/classes' );
Core::AddNamespaceTests( 'Core', __DIR__ . '/tests', true );