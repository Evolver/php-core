<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class MIME
{
    /**
     * @see http://en.wikipedia.org/wiki/Internet_media_type
     * @var string
     */
    const OCTET_STREAM = 'application/octet-stream';

    /**
     * @see http://ru.wikipedia.org/wiki/XML
     * @var string
     */
    const XML = 'application/xml';

    /**
     * @see http://en.wikipedia.org/wiki/XHTML
     * @var string
     */
    const XHTML = 'application/xhtml+xml';

    /**
     * @see http://en.wikipedia.org/wiki/Plain_text
     * @var string
     */
    const TEXT = 'text/plain';

    /**
     * @see http://en.wikipedia.org/wiki/JSON
     * @var string
     */
    const JSON = 'application/json';
}