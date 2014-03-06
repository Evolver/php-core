<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\XML\Reader;

class Document extends Element
{
    /**
     * Callback to invoke to consume DOCTYPE declaration.
     *
     * @var callable
     *
     * @see http://www.w3.org/TR/REC-xml/#NT-doctypedecl
     */
    public $onDocType;
}