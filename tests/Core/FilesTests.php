<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class FilesTests extends Tests
{
    public function GetTempDir()
    {
        $tmpDir = Files::GetTempDir();
        parent::AssertTrue( is_string( $tmpDir ) );
        parent::AssertGreater( strlen( $tmpDir ), 0 );
        parent::AssertTrue( is_dir( $tmpDir ) );

        $lastChar = substr( $tmpDir, -1 );
        parent::AssertNeq( $lastChar, '\\' );
        parent::AssertNeq( $lastChar, '/' );
    }

    public function MakeTemporary()
    {
        $tmpDir = Files::GetTempDir();

        $tmpFile = Files::MakeTemporary();
        parent::AssertTrue( is_file( $tmpFile ) );
        parent::AssertTrue( unlink( $tmpFile ) );

        $tmpFile = Files::MakeTemporary( $tmpDir );
        parent::AssertTrue( is_file( $tmpFile ) );
        parent::AssertEq( $tmpDir, dirname( $tmpFile ) );
        parent::AssertTrue( unlink( $tmpFile ) );

        $tmpFile = Files::MakeTemporary( null, 'P_' );
        $tmpFileName = basename( $tmpFile );
        parent::AssertTrue( is_file( $tmpFile ) );
        parent::AssertEq( substr( $tmpFileName, 0, 2 ), 'P_' );
        parent::AssertTrue( unlink( $tmpFile ) );

        $tmpFile = Files::MakeTemporary( $tmpDir, 'P_' );
        $tmpFileName = basename( $tmpFile );
        parent::AssertTrue( is_file( $tmpFile ) );
        parent::AssertEq( substr( $tmpFileName, 0, 2 ), 'P_' );
        parent::AssertTrue( unlink( $tmpFile ) );
    }

    public function MakeTemporaryDir()
    {
        $tmpDir = Files::GetTempDir();

        $newDir = Files::MakeTemporaryDir();
        parent::AssertTrue( is_dir( $newDir ) );
        parent::AssertTrue( rmdir( $newDir ) );

        $newDir = Files::MakeTemporaryDir( $tmpDir );
        parent::AssertTrue( is_dir( $newDir ) );
        parent::AssertEq( $tmpDir, dirname( $newDir ) );
        parent::AssertTrue( rmdir( $newDir ) );

        $newDir = Files::MakeTemporaryDir( null, 'P_' );
        $newDirName = basename( $newDir );
        parent::AssertEq( substr( $newDirName, 0, 2 ), 'P_' );
        parent::AssertTrue( rmdir( $newDir ) );

        $newDir = Files::MakeTemporaryDir( $tmpDir, 'P_' );
        $newDirName = basename( $newDir );
        parent::AssertTrue( is_dir( $newDir ) );
        parent::AssertEq( substr( $newDirName, 0, 2 ), 'P_' );
        parent::AssertTrue( rmdir( $newDir ) );
    }
}