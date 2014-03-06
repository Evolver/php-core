<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\XML;

use \XMLReader;
use \Core\Core;
use \Core\XML\Reader\Document;
use \Core\XML\Reader\Element;

class Reader extends Document
{
    /**
     * @see XMLReader::LOADDTD
     */
    const LOADDTD = XMLReader::LOADDTD;

    /**
     * @see XMLReader::DEFAULTATTRS
     */
    const DEFAULTATTRS = XMLReader::DEFAULTATTRS;

    /**
     * @see XMLReader::VALIDATE
     */
    const VALIDATE = XMLReader::VALIDATE;

    /**
     * @see XMLReader::SUBST_ENTITIES
     */
    const SUBST_ENTITIES = XMLReader::SUBST_ENTITIES;

    /**
     * @var XMLReader
     */
    protected $xml;

    /**
     * List of flags to pass to libxml.
     *
     * @var int
     */
    protected $flags = 0;

    /**
     * Currently active node.
     *
     * @var Element
     */
    protected $currentNode;

    /**
     * Parent elements of $currentNode.
     *
     * @var array
     */
    protected $context = array();

    /**
     * List of configuration options to initialize $xml with.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     */
    public function __construct( $flags = 0 )
    {
        $this->xml = new XMLReader;

        $flags |= LIBXML_PARSEHUGE;

        $this->flags = $flags;
    }

    /**
     * Assigns options to be used when parsing starts.
     *
     * @param array $options
     */
    public function SetOptions( $options = array() )
    {
        $this->options = $options;
    }

    /**
     * Parses XML stored at $URI.
     *
     * @param string $URI
     */
    public function Parse( $URI )
    {
        $xml = $this->xml;
        Core::Assert( $xml->open( $URI, null, $this->flags ) );

        try
        {
            $this->Read();
        }
        finally
        {
            $xml->close();
        }
    }

    /**
     * Parses UTF-8 XML string $xml.
     *
     * @param string $xml
     */
    public function ParseString( $xml )
    {
        $this->xml->xml( $xml, 'UTF-8', $this->flags );
        $this->Read();
    }

    /**
     * Initializes parser with options from $options.
     */
    protected function InitParser()
    {
        foreach( $this->options as $option => $value )
        {
            Core::Assert( $xml->setParserProperty( $option, $value ) );
        }

        libxml_clear_errors();
    }

    /**
     * Reads document or reads nested element (reentrant).
     *
     * @param mixed $node
     *     Rules for parsing current document context.
     *     If null, assumes new document is being read.
     *     If instanceof Element, assumes an inner element is being read.
     */
    protected function Read( $node = null )
    {
        $xml = $this->xml;

        if( $node === null )
        {
            $this->InitParser();

            $node = $this;
        }
        else
        {
            $hasContent = ( !$xml->isEmptyElement );

            if( $xml->hasAttributes && $node->onAttr )
            {
                while( $xml->moveToNextAttribute() )
                {
                    Core::Assert( $xml->nodeType == $xml::ATTRIBUTE );
                    call_user_func( $node->onAttr, $xml->localName, $xml->value, $xml->namespaceURI, $xml->prefix );
                }
            }

            if( !$hasContent )
            {
                if( $node->onEmpty )
                {
                    call_user_func( $node->onEmpty );
                }

                return;
            }
        }

        $this->EnterNode( $node );

        while( $xml->read() )
        {
            switch( $xml->nodeType )
            {
                case $xml::ELEMENT:
                {
                    $callback = $node->onElem;

                    if( $callback )
                    {
                        $callback = $callback->bindTo( $newNode = new Element );
                        $callback( $xml->localName, $xml->namespaceURI, $xml->prefix );
                        $this->Read( $newNode );
                    }
                    else
                    {
                        $xml->next();
                    }

                    break;
                }

                case $xml::END_ELEMENT:
                {
                    break 2;
                }

                case $xml::TEXT:
                {
                    if( $node->onText )
                    {
                        call_user_func( $node->onText, $xml->value );
                    }

                    break;
                }

                case $xml::CDATA:
                {
                    if( $node->onCData )
                    {
                        call_user_func( $node->onCData, $xml->value );
                    }

                    break;
                }

                case $xml::PI:
                {
                    if( $node->onProcInstruction )
                    {
                        call_user_func( $node->onProcInstruction, $xml->name, $xml->value );
                    }

                    break;
                }

                case $xml::COMMENT:
                {
                    if( $node->onComment )
                    {
                        call_user_func( $node->onComment, $xml->value );
                    }

                    break;
                }

                case $xml::DOC_TYPE:
                {
                    Core::Assert( $node instanceof Document );

                    if( $node->onDocType )
                    {
                        call_user_func( $node->onDocType, $xml->name );
                    }

                    break;
                }

                case $xml::WHITESPACE:
                case $xml::SIGNIFICANT_WHITESPACE:
                {
                    if( $node->onWhitespace )
                    {
                        call_user_func( $node->onWhitespace, $xml->value );
                    }

                    break;
                }

                case $xml::ENTITY_REF:
                {
                    if( $node->onEntity )
                    {
                        call_user_func( $node->onEntity, $xml->name );
                    }

                    break;
                }

                case $xml::DOC:
                case $xml::DOC_FRAGMENT:
                case $xml::ENTITY:

                case $xml::END_ENTITY:
                case $xml::NOTATION:
                case $xml::XML_DECLARATION:
                case $xml::ATTRIBUTE:
                {
                    Core::Fail( 'Node type ' . $xml->nodeType .' support is not implemented' );
                    break;
                }

                case $xml::NONE:
                default:
                {
                    Core::Fail( 'Unsupported node type ' . $xml->nodeType );
                }
            }
        }

        $error = libxml_get_last_error();

        if( $error !== false )
        {
            switch( $error->level )
            {
                case LIBXML_ERR_WARNING:
                {
                    break;
                }

                default:
                {
                    Core::Fail( $error->message . ' (Column ' . $error->column . ', Line ' . $error->line . ')' );
                }
            }
        }

        $this->LeaveNode();
    }

    /**
     * Starts read of content of an inner element.
     */
    protected function EnterNode( $node )
    {
        $this->context[] = $this->currentNode;
        $this->currentNode = $node;

        if( $node->onEnter )
        {
            call_user_func( $node->onEnter );
        }
    }

    /**
     * Finalizes read of content of an element.
     */
    protected function LeaveNode()
    {
        Core::Assert( !empty( $this->context ) );
        Core::Assert( $this->currentNode !== null );

        if( $this->currentNode->onLeave )
        {
            call_user_func( $this->currentNode->onLeave );
        }

        $this->currentNode = array_pop( $this->context );
    }
}