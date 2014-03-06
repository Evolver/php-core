<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class XHTMLTests extends Tests
{
    public function EscapeAttr()
    {
        parent::AssertEq( XHTML::EscapeAttr( '\'' ), '&apos;' );
        parent::AssertEq( XHTML::EscapeAttr( '"' ), '&quot;' );
        parent::AssertEq( XHTML::EscapeAttr( '<' ), '&lt;' );
        parent::AssertEq( XHTML::EscapeAttr( '>' ), '&gt;' );
        parent::AssertEq( XHTML::EscapeAttr( '<elem attr="\'">str</elem>' ),
                          '&lt;elem attr=&quot;&apos;&quot;&gt;str&lt;/elem&gt;');
        parent::AssertEq( XHTML::EscapeAttr( '東京 (Tōkyō)' ), '東京 (Tōkyō)' );
        parent::AssertEq( XHTML::EscapeAttr( '東"京 <Tōkyō>' ), '東&quot;京 &lt;Tōkyō&gt;' );
        parent::AssertEq( XHTML::EscapeAttr( 'Когда я ем, я глух и нем.' ),
                          'Когда я ем, я глух и нем.' );
    }

    public function EscapeContent()
    {
        parent::AssertEq( XHTML::EscapeContent( '\'' ), '\'' );
        parent::AssertEq( XHTML::EscapeContent( '"' ), '"' );
        parent::AssertEq( XHTML::EscapeContent( '<' ), '&lt;' );
        parent::AssertEq( XHTML::EscapeContent( '>' ), '&gt;' );
        parent::AssertEq( XHTML::EscapeContent( '<elem attr="\'">str</elem>' ),
                          '&lt;elem attr="\'"&gt;str&lt;/elem&gt;');
        parent::AssertEq( XHTML::EscapeContent( '東京 (Tōkyō)' ), '東京 (Tōkyō)' );
        parent::AssertEq( XHTML::EscapeContent( '東"京 <Tōkyō>' ), '東"京 &lt;Tōkyō&gt;' );
        parent::AssertEq( XHTML::EscapeContent( 'Когда я ем, я глух и нем.' ),
                          'Когда я ем, я глух и нем.' );
    }

    public function EscapeCData()
    {
        parent::AssertEq( XHTML::EscapeCData( '\'' ), '<![CDATA[\']]>' );
        parent::AssertEq( XHTML::EscapeCData( '"' ), '<![CDATA["]]>' );
        parent::AssertEq( XHTML::EscapeCData( '<' ), '<![CDATA[<]]>' );
        parent::AssertEq( XHTML::EscapeCData( '>' ), '<![CDATA[>]]>' );
        parent::AssertEq( XHTML::EscapeCData( '<elem attr="\'">str</elem>' ),
                          '<![CDATA[<elem attr="\'">str</elem>]]>');
        parent::AssertEq( XHTML::EscapeCData( ']]>' ),
                          '<![CDATA[]]]]><![CDATA[>]]>');
        parent::AssertEq( XHTML::EscapeCData( 'a]]>b]]>c' ),
                          '<![CDATA[a]]]]><![CDATA[>b]]]]><![CDATA[>c]]>');
        parent::AssertEq( XHTML::EscapeCData( '東京 (Tōkyō)' ), '<![CDATA[東京 (Tōkyō)]]>' );
        parent::AssertEq( XHTML::EscapeCData( '東京 ]]>Tōkyō)' ), '<![CDATA[東京 ]]]]><![CDATA[>Tōkyō)]]>' );
        parent::AssertEq( XHTML::EscapeCData( 'Когда я ем, я глух и нем.' ),
                          '<![CDATA[Когда я ем, я глух и нем.]]>' );
    }
}