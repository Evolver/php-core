<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\XML\Reader;

class Element
{
    /**
     * Callback to invoke when current element's opening tag ("<elem ") is read.
     *
     * @var callable
     */
    public $onEnter;

    /**
     * Callback to invoke when a non-empty element closing tag ("</elem>")
     * is read.
     *
     * @var callable
     */
    public $onLeave;

    /**
     * Callback to invoke when closing tag of an empty element (" />") is read.
     *
     * @var callable
     */
    public $onEmpty;

    /**
     * Callback to invoke to consume/parse a child element.
     *
     * @var callable
     */
    public $onElem;

    /**
     * Callback to invoke to consume an attribute.
     *
     * @var callable
     */
    public $onAttr;

    /**
     * Callback to invoke to consume inner "Processing Instruction".
     *
     * @var callable
     *
     * @see http://www.w3.org/TR/REC-xml/#sec-pi
     */
    public $onProcInstruction;

    /**
     * Callback to invoke to consume a set of white-space characters.
     *
     * @var callable
     */
    public $onWhitespace;

    /**
     * Callback to invoke to consume inner XML comment (<!-- ... --->).
     *
     * @var callable
     */
    public $onComment;

    /**
     * Callback to invoke to consume inner text data (XML text node).
     *
     * @var callable
     */
    public $onText;

    /**
     * Callback to invoke to consume inner CDATA section.
     *
     * @var callable
     */
    public $onCData;

    /**
     * Callback to invoke to consume inner XML entity.
     *
     * @var callable
     */
    public $onEntity;
}