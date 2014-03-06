<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core\XML;

use \XMLReader;
use \Core\XML;
use \Core\Tests;

class ReaderTests extends Tests
{
    protected function Basic_Fixtures()
    {
        // To make sure we don't substitute entities by default.
        // More info at http://php.net/manual/en/function.libxml-disable-entity-loader.php#107730
        $source = <<<XML
<!DOCTYPE scan [<!ENTITY test SYSTEM "php://filter/resource=data://text/plain,123">]>
<scan>&test;</scan>
XML;
        $output = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE scan>
<scan>&test;</scan>
XML;

        yield 'Test' => [ $source, $output ];

        $source = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?stepanov-lv processing instruction attr="type"?>
<Document xmlns="http://stepanov.lv/2013/xmlns" xml:lang="en">
    <Entry Id="1">Text</Entry>
    <Entry Id="2"><!-- Comment --> and text</Entry>
    <Entry Id="3" xml:lang="ru"><![CDATA[cdata]]> and text and &amp;</Entry>
    <Entry Id="4"></Entry>
    <Entry Id="5" />
    <Entry Name="Dmitry"
           xmlns:abc="http://test.org/abc"
           xmlns="http://stepanov.lv/2013/xmlns/2"
           Id="6"
           xmlns:test="http://test.org"><abc:x /><test:z /></Entry>
</Document>
XML;

        // For some reason, XMLReader will move xmlns attributes to
        // the beginning of attribute list
        $output = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?stepanov-lv processing instruction attr="type"?>
<Document xmlns="http://stepanov.lv/2013/xmlns" xml:lang="en">
    <Entry Id="1">Text</Entry>
    <Entry Id="2"><!-- Comment --> and text</Entry>
    <Entry Id="3" xml:lang="ru"><![CDATA[cdata]]> and text and &amp;</Entry>
    <Entry Id="4"></Entry>
    <Entry Id="5" />
    <Entry xmlns:abc="http://test.org/abc" xmlns="http://stepanov.lv/2013/xmlns/2" xmlns:test="http://test.org" Name="Dmitry" Id="6"><abc:x /><test:z /></Entry>
</Document>
XML;

        yield 'Small' => [ $source, $output ];

        $source = <<<HERE
<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<html>
<body>
  <h2>My CD Collection</h2>
  <table border="1">
    <tr>
      <th style="text-align:left">Title</th>
      <th style="text-align:left">Artist</th>
    </tr>
    <xsl:for-each select="catalog/cd">
    <tr>
      <td><xsl:value-of select="title"/></td>
      <td><xsl:value-of select="artist"/></td>
    </tr>
    </xsl:for-each>
  </table>
</body>
</html>
</xsl:template>

</xsl:stylesheet>
HERE;
        $output = <<<HERE
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template match="/">
<html>
<body>
  <h2>My CD Collection</h2>
  <table border="1">
    <tr>
      <th style="text-align:left">Title</th>
      <th style="text-align:left">Artist</th>
    </tr>
    <xsl:for-each select="catalog/cd">
    <tr>
      <td><xsl:value-of select="title" /></td>
      <td><xsl:value-of select="artist" /></td>
    </tr>
    </xsl:for-each>
  </table>
</body>
</html>
</xsl:template>

</xsl:stylesheet>
HERE;

        yield 'Medium' => [ $source, $output ];

        $source = <<<XML
<?xml version="1.0"?>
<?xml-stylesheet href="catalog.xsl" type="text/xsl"?>
<!DOCTYPE catalog SYSTEM "catalog.dtd">
<catalog>
   <product description="Cardigan Sweater" product_image="cardigan.jpg">
      <catalog_item gender="Men's">
         <item_number>QWZ5671</item_number>
         <price>39.95</price>
         <size description="Medium">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
         <size description="Large">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
      </catalog_item>
      <catalog_item gender="Women's">
         <item_number>RRX9856</item_number>
         <price>42.50</price>
         <size description="Small">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
         <size description="Medium">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
         <size description="Large">
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
         <size description="Extra Large">
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
      </catalog_item>
   </product>
</catalog>
XML;
        $output = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="catalog.xsl" type="text/xsl"?>
<!DOCTYPE catalog>
<catalog>
   <product description="Cardigan Sweater" product_image="cardigan.jpg">
      <catalog_item gender="Men&apos;s">
         <item_number>QWZ5671</item_number>
         <price>39.95</price>
         <size description="Medium">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
         <size description="Large">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
      </catalog_item>
      <catalog_item gender="Women&apos;s">
         <item_number>RRX9856</item_number>
         <price>42.50</price>
         <size description="Small">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
         </size>
         <size description="Medium">
            <color_swatch image="red_cardigan.jpg">Red</color_swatch>
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
         <size description="Large">
            <color_swatch image="navy_cardigan.jpg">Navy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
         <size description="Extra Large">
            <color_swatch image="burgundy_cardigan.jpg">Burgundy</color_swatch>
            <color_swatch image="black_cardigan.jpg">Black</color_swatch>
         </size>
      </catalog_item>
   </product>
</catalog>
XML;

        yield 'Large' => [ $source, $output ];
    }

    public function Basic( $source, $output )
    {
        $parser = $this->GetRetranslatingParser();

        ob_start();
        try
        {
            $parser->ParseString( $source );
            parent::AssertEq( $output, ob_get_contents() );
        }
        finally
        {
            ob_end_clean();
        }
    }

    protected function GetRetranslatingParser()
    {
        $xml = new Reader;

        $xml->onEnter = function()
        {
           echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        };

        $xml->onLeave = function()
        {
        };

        $xml->onDocType = function( $type )
        {
            echo '<!DOCTYPE ' . $type . '>' . "\n";
        };

        $xml->onProcInstruction = function( $type, $instruction )
        {
            echo '<?' . $type . ' ' . $instruction . '?>' . "\n";
        };

        $this->onWhitespace = function( $value )
        {
            echo XML::EscapeContent( $value );
        };

        $this->onText = function( $value )
        {
            echo XML::EscapeContent( $value );
        };

        $this->onCData = function( $value )
        {
            echo XML::EscapeCData( $value );
        };

        $this->onComment = function( $value )
        {
            echo '<!--' . XML::EscapeContent( $value ) . '-->';
        };

        $this->onEntity = function( $name )
        {
            echo '&' . $name . ';';
        };

        $onElem = null;
        $onElem = function( $name, $xmlns, $prefix ) use( &$onElem )
        {
            echo '<' . ( $prefix ? $prefix . ':' : '' ) . $name;

            $this->onAttr = function( $name, $value, $xmlns, $prefix )
            {
                echo ' ' . ( $prefix ? $prefix . ':' : '' ) . $name . '="' . XML::EscapeAttr( $value ) . '"';
            };

            $this->onEnter = function()
            {
                echo '>';
            };

            $this->onLeave = function() use( $name, $prefix )
            {
                echo '</' . ( $prefix ? $prefix . ':' : '' ) . $name .'>';
            };

            $this->onEmpty = function()
            {
                echo ' />';
            };

            $this->onWhitespace = function( $value )
            {
                echo XML::EscapeContent( $value );
            };

            $this->onText = function( $value )
            {
                echo XML::EscapeContent( $value );
            };

            $this->onCData = function( $value )
            {
                echo XML::EscapeCData( $value );
            };

            $this->onComment = function( $value )
            {
                echo '<!--' . XML::EscapeContent( $value ) . '-->';
            };

            $this->onEntity = function( $name )
            {
                echo '&' . $name . ';';
            };

            $this->onElem = &$onElem;
        };

        $xml->onElem = $onElem;

        return $xml;
    }
}