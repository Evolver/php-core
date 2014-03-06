<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

use \Exception;
use \Closure;

class Core
{
    public static $namespaceRegex = '(?:[a-zA-Z0-9_\\-]+\\\\)*[a-zA-Z0-9_\\-]+';
    public static $nameRegex = '[a-zA-Z0-9_\\-]+';

    // Initialized later
    public static $fullyQualifiedNameRegex;

    /**
     * Whether to enable extra debug checks.
     *
     * @var boolean
     */
    public static $debug = true;

    /**
     * List of name autoloader functions.
     *
     * @var array
     */
    protected static $autoloaders = array();

    /**
     * Emits standard exception with the given error message.
     *
     * @param string $message
     *
     * @param int $code
     *
     * @param Exception $prev
     *     Previous exception to form exception chain with.
     */
    public static function Fail( $message, $code = 0, $prev = null )
    {
        throw new Exception( $message, $code, $prev );
    }

    /**
     * Ensures $cond === true. Otherwise, throws standard exception
     * with message given in $message.
     *
     * The assertion checks performed by this function are never disabled
     * and ARE NOT INTENDED to ever be disabled.
     *
     * It is safe to rely on this function to perform critical security
     * checks.
     *
     * If https://wiki.php.net/rfc/expectations are to be implemented, some
     * uses of this function may need be switched to assert() in order to
     * optimize performance in production environment.
     *
     * @param bool $cond
     *
     * @param mixed $message
     *     If null, a default message is used. Otherwise, whatever
     *     was passed is used as a message.
     */
    public static function Assert( $cond, $message = null )
    {
        if( $cond === true )
        {
            return;
        }

        if( $message === null )
        {
            $message = 'Assertion failed';
        }

        static::Fail( $message );
    }

    /**
     * Adds autoloading function.
     * Callback should behave the same way as with spl_autoload_register().
     * $callback can be passed to Core::Remove() to unregister the autoloading
     * function.
     *
     * @param Closure $callback
     *
     * @param boolean $prepend
     *     Whether to add the callback to the beginning of the callback list
     *     or push at the end.
     *
     * @throws
     *     $callback could not be registered or is already registered
     *     in the autoload stack.
     *
     * @see http://www.php.net/function.spl-autoload-register
     * @see http://www.php.net/function.spl-autoload-functions
     */
    public static function Add( $callback, $prepend = false )
    {
        static::Assert( $callback instanceof Closure );

        if( in_array( $callback, static::$autoloaders, true ) )
        {
            static::Fail( 'Callback already added' );
        }

        if( $prepend )
        {
            array_unshift( static::$autoloaders, $callback );
        }
        else
        {
            static::$autoloaders[] = $callback;
        }
    }

    /**
     * Removes the specified function from the autoloader.
     *
     * @param Closure $callback
     *
     * @return boolean
     */
    public static function Remove( $callback )
    {
        static::Assert( $callback instanceof Closure );

        foreach( static::$autoloaders as $i => $function )
        {
            if( $function === $callback )
            {
                unset( static::$autoloaders[ $i ] );
                return true;
            }
        }

        return false;
    }

    /**
     * Loads class, interface or trait identified by $name.
     *
     * @param string $name
     *     Fully-qualified name.
     *
     * @return boolean
     */
    public static function LoadName( $name )
    {
        if( static::IsNameLoaded( $name ) )
        {
            return true;
        }

        if( preg_match( '/^' . static::$fullyQualifiedNameRegex . '$/', $name ) !== 1 )
        {
            static::Fail( 'Malformed name' );
        }

        foreach( static::$autoloaders as $function )
        {
            $function( $name );

            if( static::IsNameLoaded( $name ) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds a directory to load classes, interfaces or traits from.
     * Names are loaded only if matched against $regex.
     *
     * @param string $regex
     *     Regular expression to match against names.
     *
     * @param string $path
     *     Path to directory where name files are located.
     *
     * @param boolean $prepend
     *     If false, $path will be tried last in the current
     *     autoloading callback list.
     *     If true, $path will be tried first.
     *
     * @return
     *     Closure that can be passed to Core::Remove() to
     *     stop further autoloading.
     */
    public static function AddNames( $regex, $path, $prepend = false )
    {
        $handler = function( $name ) use( $regex, $path )
        {
            if( preg_match( $regex, $name ) !== 1 )
            {
                return;
            }

            $classPath = str_replace( '\\', '/', $name );
            include( $path .'/' .$classPath . '.php' );
        };

        static::Add( $handler, $prepend );

        return $handler;
    }

    /**
     * Adds a directory to load names belonging to the specified
     * namespace from.
     *
     * @param string $ns
     *     Namespace.
     *
     * @param string $path
     *     Path to directory where name files are located.
     *
     * @param boolean $prepend
     *     If false, $path will be tried last in the current
     *     autoloading callback list.
     *     If true, $path will be tried first.
     *
     * @return
     *     Closure that can be passed to Core::Remove() to
     *     stop further autoloading.
     */
    public static function AddNamespace( $ns, $path, $prepend = false )
    {
        if( $ns == '' )
        {
            $regex = '/.+/';
        }
        else
        {
            $regex = '/^' . str_replace( '\\', '\\\\', $ns ) .'\\\\/';
        }

        return static::AddNames( $regex, $path, $prepend );
    }

    /**
     * Adds a directory to load test classes belonging to the
     * specified namespace from. Test classes are classes that
     * end with "Test" string.
     *
     * @param string $ns
     *     Namespace.
     *
     * @param string $path
     *     Path to directory where class files are located.
     *
     * @param boolean $prepend
     *     If false, $path will be tried last in the current
     *     autoloading callback list.
     *     If true, $path will be tried first.
     *
     * @return
     *     Closure that can be passed to Core::Remove() to
     *     stop further autoloading.
     */
    public static function AddNamespaceTests( $ns, $path, $prepend = false )
    {
        $regex = '/';

        if( $ns != '' )
        {
            $regex .= '^' . str_replace( '\\', '\\\\', $ns ) .'\\\\';
        }

        $regex .= '.+(?<!\\\\)Tests$/';

        return static::AddNames( $regex, $path, $prepend );
    }

    /**
     * Checks whether a name is loaded. $name represents either
     * a class, interface or a trait.
     *
     * This function does not kick off autoloader.
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function IsNameLoaded( $name )
    {
        return class_exists( $name, false ) ||
               interface_exists( $name, false ) ||
               trait_exists( $name, false );
    }

    /**
     * Returns URI that refers to server that is hosting current
     * application.
     *
     * @throws \Exception
     *     Server URI could not be determined or environment is not
     *     supported.
     */
    public static function GetServerURI()
    {
        static::Assert( array_key_exists( 'SERVER_PROTOCOL', $_SERVER ) );
        static::Assert( array_key_exists( 'SERVER_NAME', $_SERVER ) );
        static::Assert( array_key_exists( 'SERVER_PORT', $_SERVER ) );

        switch( $proto = $_SERVER['SERVER_PROTOCOL'] )
        {
            case 'HTTP/1.0':
            case 'HTTP/1.1':
            case 'HTTP/2.0':
            {
                if( ( !array_key_exists( 'HTTPS', $_SERVER ) ) ||
                    ( strcasecmp( $_SERVER[ 'HTTPS' ], 'off' ) == 0 ) )
                {
                    $scheme = 'http';
                    $port = 80;
                }
                else
                {
                    $scheme = 'https';
                    $port = 443;
                }

                break;
            }

            default:
            {
                static::Fail( 'Unsupported protocol ' . $proto );
            }
        }

        if( $port == $_SERVER[ 'SERVER_PORT' ] )
        {
            $port = null;
        }
        else
        {
            $port = $_SERVER[ 'SERVER_PORT' ];
        }

        $URI = $scheme . '://' . $_SERVER[ 'SERVER_NAME' ];

        if( $port !== null )
        {
            $URI .= ':' . $port;
        }

        return $URI;
    }

    /**
     * Returns absolute public base URI for the specified directory.
     * $dir must be exposed to public document root.
     *
     * Utilizes $_SERVER[ 'DOCUMENT_ROOT' ].
     *
     * @param mixed $dir
     *     If string given, treated as absolute path that has to be
     *     a child of $_SERVER[ 'DOCUMENT_ROOT' ].
     *     If null given, returns value of GetServerURI().
     *
     * @return string
     *
     * @throws \Exception
     *     $dir is not exposed to document root, URI could not be
     *     determined or the environment is not supported.
     */
    public static function GetBaseURI( $dir = null )
    {
        static::Assert( array_key_exists( 'DOCUMENT_ROOT', $_SERVER ),
                        'No document root defined' );

        if( $dir !== null )
        {
            $docRoot = $_SERVER[ 'DOCUMENT_ROOT' ];
            $docRootLastChar = substr( $docRoot, -1 );

            if( $docRootLastChar == '/' || $docRootLastChar == '\\' )
            {
                // Strip ending slashes off the docroot path
                $docRoot = dirname( $docRoot );
            }

            $docRootLen = strlen( $docRoot );
            $dirLen = strlen( $dir );

            static::Assert( $dirLen >= $docRootLen,
                            '$dir is not a child of $docRoot' );
            static::Assert( $docRoot === substr( $dir, 0, $docRootLen ),
                            '$dir is not a child of $docRoot' );

            $path = substr( $dir, $docRootLen );

            if( $path != '' )
            {
                static::Assert( $path[ 0 ] == '/', 'Non-empty path shall start with forward slash' );
            }
        }
        else
        {
            $path = '';
        }

        return static::GetServerURI() . $path;
    }

    /**
     * Returns count of seconds passed since start of request processing.
     *
     * @return float
     */
    public static function GetRequestDuration()
    {
        static::Assert( array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER ) );

        return microtime( true ) - $_SERVER[ 'REQUEST_TIME_FLOAT' ];
    }

    /**
     * Converts relative $path to name path, optionally prefixing with $prefix.
     *
     * @param string $path
     *
     * @param string $prefix
     *
     * @return string
     */
    public static function PathToName( $path, $prefix = '' )
    {
         return $prefix . str_replace( '/', '\\', $path );
    }
}

Core::$fullyQualifiedNameRegex =
    '(?:(?P<ns>' . Core::$namespaceRegex . ')\\\\)?' .
    '(?P<name>' . Core::$nameRegex . ')';

Core::Assert( spl_autoload_register( 'Core\\Core::LoadName' ) === true );
