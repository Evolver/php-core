<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class XMLTests extends Tests
{
    public function EscapeAttr()
    {
        parent::AssertEq( XML::EscapeAttr( '\'' ), '&apos;' );
        parent::AssertEq( XML::EscapeAttr( '"' ), '&quot;' );
        parent::AssertEq( XML::EscapeAttr( '<' ), '&lt;' );
        parent::AssertEq( XML::EscapeAttr( '>' ), '&gt;' );
        parent::AssertEq( XML::EscapeAttr( '<elem attr="\'">str</elem>' ),
                          '&lt;elem attr=&quot;&apos;&quot;&gt;str&lt;/elem&gt;');
        parent::AssertEq( XML::EscapeAttr( '東京 (Tōkyō)' ), '東京 (Tōkyō)' );
        parent::AssertEq( XML::EscapeAttr( '東"京 <Tōkyō>' ), '東&quot;京 &lt;Tōkyō&gt;' );
        parent::AssertEq( XML::EscapeAttr( 'Когда я ем, я глух и нем.' ),
                          'Когда я ем, я глух и нем.' );
    }

    public function EscapeContent()
    {
        parent::AssertEq( XML::EscapeContent( '\'' ), '\'' );
        parent::AssertEq( XML::EscapeContent( '"' ), '"' );
        parent::AssertEq( XML::EscapeContent( '<' ), '&lt;' );
        parent::AssertEq( XML::EscapeContent( '>' ), '&gt;' );
        parent::AssertEq( XML::EscapeContent( '<elem attr="\'">str</elem>' ),
                          '&lt;elem attr="\'"&gt;str&lt;/elem&gt;');
        parent::AssertEq( XML::EscapeContent( '東京 (Tōkyō)' ), '東京 (Tōkyō)' );
        parent::AssertEq( XML::EscapeContent( '東"京 <Tōkyō>' ), '東"京 &lt;Tōkyō&gt;' );
        parent::AssertEq( XML::EscapeContent( 'Когда я ем, я глух и нем.' ),
                          'Когда я ем, я глух и нем.' );
    }

    public function EscapeCData()
    {
        parent::AssertEq( XML::EscapeCData( '\'' ), '<![CDATA[\']]>' );
        parent::AssertEq( XML::EscapeCData( '"' ), '<![CDATA["]]>' );
        parent::AssertEq( XML::EscapeCData( '<' ), '<![CDATA[<]]>' );
        parent::AssertEq( XML::EscapeCData( '>' ), '<![CDATA[>]]>' );
        parent::AssertEq( XML::EscapeCData( '<elem attr="\'">str</elem>' ),
                          '<![CDATA[<elem attr="\'">str</elem>]]>');
        parent::AssertEq( XML::EscapeCData( ']]>' ),
                          '<![CDATA[]]]]><![CDATA[>]]>');
        parent::AssertEq( XML::EscapeCData( 'a]]>b]]>c' ),
                          '<![CDATA[a]]]]><![CDATA[>b]]]]><![CDATA[>c]]>');
        parent::AssertEq( XML::EscapeCData( '東京 (Tōkyō)' ), '<![CDATA[東京 (Tōkyō)]]>' );
        parent::AssertEq( XML::EscapeCData( '東京 ]]>Tōkyō)' ), '<![CDATA[東京 ]]]]><![CDATA[>Tōkyō)]]>' );
        parent::AssertEq( XML::EscapeCData( 'Когда я ем, я глух и нем.' ),
                          '<![CDATA[Когда я ем, я глух и нем.]]>' );
    }
}