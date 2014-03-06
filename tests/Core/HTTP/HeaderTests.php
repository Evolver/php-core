<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\HTTP;

use \Exception;
use \Core\Tests;

class HeaderTests extends Tests
{
    public function Status()
    {
        parent::AssertEq( Header::Status( 200, 'OK', 'HTTP/1.1' ), "HTTP/1.1 200 OK" );
        parent::AssertEq( Header::Status( 403, 'Forbidden', 'HTTP/1.0' ), "HTTP/1.0 403 Forbidden" );

        parent::Context( [ & $_SERVER ], function()
        {
            unset( $_SERVER[ 'SERVER_PROTOCOL'] );
            parent::AssertThrows( function()
            {
                Header::Status( 200, 'OK' );
            });

            $_SERVER[ 'SERVER_PROTOCOL' ] = 'XXXX/1.2';
            parent::AssertEq( Header::Status( 200, 'OK' ), "XXXX/1.2 200 OK" );
        });
    }

    public function ContentType()
    {
        parent::AssertTrue( false );
    }

    public function ContentDisposition()
    {
        parent::AssertTrue( false );
    }
}