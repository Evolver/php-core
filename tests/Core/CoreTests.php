<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class CoreTests extends Tests
{
    public function Assertions()
    {
        Core::Assert( true );

        parent::AssertThrows( function()
        {
            Core::Assert( false );
        });
    }

    public function Failures()
    {
        parent::AssertThrows( function()
        {
            Core::Fail( 'Synthetic error' );
        });
    }

    public function IsNameLoaded()
    {
        parent::AssertTrue( Core::IsNameLoaded( 'stdClass' ) );
        parent::AssertTrue( Core::IsNameLoaded( 'ArrayAccess' ) );
        parent::AssertTrue( Core::IsNameLoaded( 'Core\\__DummyTrait' ) );
        parent::AssertFalse( Core::IsNameLoaded( 'Core\\__FAKE_CLASS_1' ) );
    }

    public function LoadClass()
    {
        parent::AssertThrows( function()
        {
            Core::LoadName( 'Core\\SomeNs\\..\\InvalidClass' );
        });
    }

    public function AddAndRemove()
    {
        $function = function( $name ) { };

        Core::Add( $function );

        parent::AssertThrows( function() use( $function )
        {
            Core::Add( $function );
        });

        parent::AssertTrue( Core::Remove( $function ) );
        parent::AssertFalse( Core::Remove( $function ) );

        parent::AssertThrows( function() use( $function )
        {
            Core::Add( static::class . '::Remove_Dummy' );
        });

        parent::AssertThrows( function() use( $function )
        {
            Core::Add( array( static::class, '::Remove_Dummy' ) );
        });

        Core::Add( $function );
        Core::Remove( $function );
    }

    public function AddNames()
    {
        $rootDir = Files::MakeTemporaryDir();

        try
        {
            mkdir( $dummyNsDir = $rootDir . '/DummyNamespace' );
            parent::AssertTrue( is_dir( $dummyNsDir ) );

            try
            {
                mkdir( $dummySubNsDir = $dummyNsDir . '/SubNamespace' );
                parent::AssertTrue( is_dir( $dummySubNsDir ) );

                try
                {
                    $classFile1 = $dummyNsDir . '/Class1.php';
                    $classCode1 = '<?php namespace DummyNamespace; class Class1 {}';

                    parent::AssertNotFalse( file_put_contents( $classFile1, $classCode1 ) );

                    try
                    {
                        $classFile2 = $dummySubNsDir . '/Class2.php';
                        $classCode2 = '<?php namespace DummyNamespace\\SubNamespace; class Class2 {}';

                        parent::AssertNotFalse( file_put_contents( $classFile2, $classCode2 ) );

                        try
                        {
                            $classFile3 = $dummyNsDir . '/Warmup.php';
                            $classCode3 = '<?php namespace DummyNamespace; class Warmup {}';

                            parent::AssertNotFalse( file_put_contents( $classFile3, $classCode3 ) );

                            try
                            {
                                parent::AssertFalse( Core::LoadName( 'DummyNamespace\\Warmup' ) );

                                $callback = Core::AddNames( $regex = '/^DummyNamespace\\\\.+$/', $rootDir );
                                try
                                {
                                    parent::AssertTrue( Core::LoadName( 'DummyNamespace\\Warmup' ) );
                                }
                                finally
                                {
                                    Core::Remove( $callback );
                                }

                                parent::AssertFalse( Core::LoadName( 'DummyNamespace\\Class1' ) );

                                $callback = Core::AddNames( $regex, $rootDir );
                                try
                                {
                                    parent::AssertTrue( Core::LoadName( 'DummyNamespace\\Class1' ) );
                                    parent::AssertTrue( Core::LoadName( 'DummyNamespace\\SubNamespace\\Class2' ) );
                                }
                                finally
                                {
                                    Core::Remove( $callback );
                                }
                            }
                            finally
                            {
                                unlink( $classFile3 );
                            }
                        }
                        finally
                        {
                            unlink( $classFile2 );
                        }
                    }
                    finally
                    {
                        unlink( $classFile1 );
                    }
                }
                finally
                {
                    rmdir( $dummySubNsDir );
                }
            }
            finally
            {
                rmdir( $dummyNsDir );
            }
        }
        finally
        {
            rmdir( $rootDir );
        }
    }

    public function AddNamespace()
    {
        Core::Remove( Core::AddNamespace( 'DummyNamespace', '/tmp/non/existent/path' ) );
        Core::Remove( Core::AddNamespace( 'DummyNamespace', '/tmp/non/existent/path', true ) );
    }

    public function AddNamespaceTests()
    {
        Core::Remove( Core::AddNamespaceTests( 'DummyNamespace', '/tmp/non/existent/path' ) );
        Core::Remove( Core::AddNamespaceTests( 'DummyNamespace', '/tmp/non/existent/path', true ) );
    }

    public function GetServerURI()
    {
        parent::AssertNoThrow( function()
        {
            Core::GetServerURI();
        });

        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'SERVER_PROTOCOL' ] );
            parent::AssertThrows( function()
            {
                Core::GetServerURI();
            });
        });

        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'SERVER_NAME' ] );
            parent::AssertThrows( function()
            {
                Core::GetServerURI();
            });
        });

        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'SERVER_PORT' ] );
            parent::AssertThrows( function()
            {
                Core::GetServerURI();
            });
        });

        // HTTP
        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'HTTPS' ] );
            $_SERVER[ 'SERVER_NAME' ] = 'stepanov.lv';
            $_SERVER[ 'SERVER_PORT' ] = 80;
            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/1.0';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv' );

            $_SERVER[ 'HTTPS' ] = 'off';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv' );

            unset( $_SERVER[ 'HTTPS' ] );
            $_SERVER[ 'SERVER_PORT' ] = 81;
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );

            $_SERVER[ 'HTTPS' ] = 'off';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );

            unset( $_SERVER[ 'HTTPS' ] );
            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/1.1';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );

            $_SERVER[ 'HTTPS' ] = 'off';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );

            unset( $_SERVER[ 'HTTPS' ] );
            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/2.0';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );

            $_SERVER[ 'HTTPS' ] = 'off';
            parent::AssertEq( Core::GetServerURI(), 'http://stepanov.lv:81' );
        });

        // HTTPs
        parent::Context( [ & $_SERVER ], function()
        {
            $_SERVER[ 'HTTPS' ] = 'on';
            $_SERVER[ 'SERVER_NAME' ] = 'stepanov.lv';
            $_SERVER[ 'SERVER_PORT' ] = 443;
            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/1.0';
            parent::AssertEq( Core::GetServerURI(), 'https://stepanov.lv' );

            $_SERVER[ 'SERVER_PORT' ] = 80;
            parent::AssertEq( Core::GetServerURI(), 'https://stepanov.lv:80' );

            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/1.1';
            parent::AssertEq( Core::GetServerURI(), 'https://stepanov.lv:80' );

            $_SERVER[ 'SERVER_PROTOCOL' ] = 'HTTP/2.0';
            parent::AssertEq( Core::GetServerURI(), 'https://stepanov.lv:80' );
        });
    }

    public function GetBaseURI()
    {
        parent::AssertNoThrow( function()
        {
            Core::GetBaseURI();
        });

        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'DOCUMENT_ROOT' ] );
            parent::AssertThrows( function()
            {
                Core::GetBaseURI();
            });
        });

        parent::Context( [ & $_SERVER ], function()
        {
            $serverURI = Core::GetServerURI();
            $_SERVER[ 'DOCUMENT_ROOT' ] = '/var/www';

            parent::AssertEq( Core::GetBaseURI(), $serverURI );
            parent::AssertEq( Core::GetBaseURI( '/var/www' ),
                              $serverURI );
            parent::AssertEq( Core::GetBaseURI( '/var/www/' ),
                              $serverURI . '/' );
            parent::AssertEq( Core::GetBaseURI( '/var/www/index.php' ),
                              $serverURI . '/index.php' );
            parent::AssertEq( Core::GetBaseURI( '/var/www/name with spaces.php' ),
                              $serverURI . '/name with spaces.php' );

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/var/ww' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/var/wwW' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/var/wwwW' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/var/wwww' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( 'var/www' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '.' );
            });

            parent::AssertThrows( function()
            {
                Core::GetBaseURI( '/var/../var/www' );
            });
        });
    }
}

// Used in unit tests.
// If PHP provides any built-in trait, then remove this
// dummy and use built-in instead.
trait __DummyTrait
{
}