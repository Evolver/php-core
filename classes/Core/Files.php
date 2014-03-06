<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class Files
{
    /**
     * Creates a file with unique file name in the specified $dir.
     * If $dir is null, Files::GetTempDir() will be used.
     *
     * @param mixed $dir
     *     If string, will be used as target directory.
     *     If null, will fall back to Files::GetTempDir().
     *
     * @param mixed $prefix
     *     If string, will be used to prefix temporary file name.
     *     If null, no prefix will be used.
     *
     * @return
     *     Absolute path to the newly created temporary file.
     */
    public static function MakeTemporary( $dir = null, $prefix = null )
    {
        if( $dir === null )
        {
            $dir = static::GetTempDir();
        }

        $path = tempnam( $dir, $prefix );
        Core::Assert( $path !== false );

        return $path;
    }

    /**
     * Creates a directory with unique name in the specified $dir.
     * If $dir is null, Files::GetTempDir() will be used.
     *
     * @param mixed $dir
     *     If string, will be used as target directory.
     *     If null, will fall back to Files::GetTempDir().
     *
     * @param mixed $prefix
     *     If string, will be used to prefix temporary dir name.
     *     If null, no prefix will be used.
     *
     * @return
     *     Absolute path to the newly created temporary directory.
     */
    public static function MakeTemporaryDir( $dir = null, $prefix = null )
    {
        if( $dir === null )
        {
            $dir = static::GetTempDir();
        }

        while( true )
        {
            $randomPath = $dir . '/';

            if( $prefix !== null )
            {
                $randomPath .= $prefix;
            }

            $randomPath .= microtime( true ) .'_' . mt_rand( 100000, 999999 );

            if( file_exists( $randomPath ) )
            {
                continue;
            }

            Core::Assert( mkdir( $randomPath ) );
            break;
        }

        return $randomPath;
    }

    /**
     * Returns absolute path to temporary file directory.
     *
     * @return string
     */
    public static function GetTempDir()
    {
        return sys_get_temp_dir();
    }
}