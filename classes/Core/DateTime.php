<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

use \DateTime as BaseDateTime;
use \DateTimeZone;

class DateTime extends BaseDateTime
{
    const FORMAT_ISO8601 = 'c';
    const FORMAT_HTML5_DATETIME = 'c';

    /**
     * Returns current time.
     *
     * @param DateTimeZone $timezone
     *
     * @return DateTime
     */
    public static function Now( $timezone = null )
    {
        return new static( 'now', $timezone );
    }
}