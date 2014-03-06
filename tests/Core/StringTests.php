<?php
/**
 * @author Dmitry Stepanov <dmitry@stepanov.lv>
 * @copyright 2013, Dmitry Stepanov. All rights reserved.
 * @link http://stepanov.lv
 */

namespace Core;

class StringTests extends Tests
{
    public function Length()
    {
        parent::AssertEq( String::Length( '' ), 0 );

        // Japanese
        parent::AssertEq( String::Length( 'かゐ卧 禞わ覟鋨' ), 8 );
        parent::AssertEq( String::Length( '䤣鰧げ 奞秤つむや 馤ちゅ, 榵䧦 驚鄢' ), 20 );
        parent::AssertEq( String::Length( 'ェ すフィェ, 褚焩ウ 稞' ), 13 );

        // Chinese
        parent::AssertEq( String::Length( '絼綒 櫧櫋瀩, 膗 圛' ), 11 );
        parent::AssertEq( String::Length( ' 孻憵懥 嬽巃攇 皾籈譧 眅' ), 14 );
        parent::AssertEq( String::Length( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 墏 紽翍 ' ), 21 );

        // Korean
        parent::AssertEq( String::Length( '하는 아시아 여러 국가에서는 한국어를 가리' ), 23 );
        parent::AssertEq( String::Length( ' 경향이 있기 때문에' ), 11 );
        parent::AssertEq( String::Length( '슷하다. 현재는 한자를 사용하지 ' ), 18 );

        // Russian
        parent::AssertEq( String::Length( 'Хаж ад унюм емпыдит вэртырэм' ), 28 );
        parent::AssertEq( String::Length( 'нюлльам жэмпэр ыкжплььикари, мэя ад мёнём' ), 41 );
        parent::AssertEq( String::Length( ' эи дуо. Квуй дэниквюы тхэопхр' ), 30 );

        // English
        parent::AssertEq( String::Length( 'Dmitry Stepanov 123' ), 19 );
        parent::AssertEq( String::Length( 'http://stepanov.lv' ), 18 );

        // Latvian
        parent::AssertEq( String::Length( 'Krīze nozīmē neskaidro procesu evolūciju,' ), 41 );
        parent::AssertEq( String::Length( 'Ekonomiskā krīze ir nozīmīga ekonomikas cikla fāze.' ), 51 );
        parent::AssertEq( String::Length( 'Baltijas tīģeris' ), 16 );

        // Mixed
        parent::AssertEq( String::Length( 'かゐ卧櫧櫋瀩경향이ыкжDmitīģ' ), 18 );
    }

    public function Pos()
    {
        // Japanese
        parent::AssertEq( String::Pos( 'かゐ卧 禞わ覟鋨', '卧' ), 2 );
        parent::AssertEq( String::Pos( '䤣鰧げ 奞秤つむや 馤ちゅ, 榵䧦 驚鄢', 'や 馤' ), 8 );
        parent::AssertEq( String::Pos( 'ェ すフィェ, 褚焩ウ 稞', '褚焩' ), 8 );

        parent::AssertEq( String::Pos( 'かゐ卧 禞卧覟鋨', '卧', 3 ), 5 );
        parent::AssertEq( String::Pos( '䤣鰧げ 奞秤つむや 馤ちゅ,や 馤 驚鄢', 'や 馤', 9 ), 14 );
        parent::AssertEq( String::Pos( 'ェ 褚焩ィェ, 褚焩ウ 稞', '褚焩', 3 ), 8 );

        // Chinese
        parent::AssertEq( String::Pos( '絼綒 櫧櫋瀩, 膗 圛', '膗' ), 8 );
        parent::AssertEq( String::Pos( ' 孻憵懥 嬽巃攇 皾籈譧 眅', '譧 眅' ), 11 );
        parent::AssertEq( String::Pos( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 墏 紽翍 ', '蓌蓖' ), 4 );

        parent::AssertEq( String::Pos( '絼膗 櫧櫋瀩, 膗 圛', '膗', 2 ), 8 );
        parent::AssertEq( String::Pos( ' 譧 眅 嬽巃攇 皾籈譧 眅', '譧 眅', 2 ), 11 );
        parent::AssertEq( String::Pos( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 蓌蓖紽翍 ', '蓌蓖', 5 ), 16 );

        // Korean
        parent::AssertEq( String::Pos( '하는 아시아 여러 국가에서는 한국어를 가리', '에' ), 12 );
        parent::AssertEq( String::Pos( ' 경향이 있기 때문에', '기 때' ), 6 );
        parent::AssertEq( String::Pos( '슷하다. 현재는 한자를 사용하지 ', '사용' ), 13 );

        parent::AssertEq( String::Pos( '하는 아에아 여러 국가에서는 한국어를 가리', '에', 5 ), 12 );
        parent::AssertEq( String::Pos( ' 경기 때있기 때문에', '기 때', 3 ), 6 );
        parent::AssertEq( String::Pos( '슷하다. 사용는 한자를 사용하지 ', '사용', 6 ), 13 );

        // Russian
        parent::AssertEq( String::Pos( 'Хаж ад унюм емпыдит вэртырэм', 'ыди' ), 15 );
        parent::AssertEq( String::Pos( 'нюлльам жэмпэр ыкжплььикари, мэя ад мёнём', 'ьи' ), 21 );
        parent::AssertEq( String::Pos( ' эи дуо. Квуй дэниквюы тхэопхр', 'иквю' ), 17 );

        parent::AssertEq( String::Pos( 'Хажыди унюм емпыдит вэртырэм', 'ыди', 4 ), 15 );
        parent::AssertEq( String::Pos( 'нюлльаьижэмпэр ыкжплььикари, мэя ад мёнём', 'ьи', 7 ), 21 );
        parent::AssertEq( String::Pos( 'иквюдуо. Квуй дэниквюы тхэопхр', 'иквю', 1 ), 17 );

        // English
        parent::AssertEq( String::Pos( 'Dmitry Stepanov 123', 'a' ), 11 );
        parent::AssertEq( String::Pos( 'http://stepanov.lv', 'lv' ), 16 );

        parent::AssertEq( String::Pos( 'amitry Stepanov 123', 'a', 1 ), 11 );
        parent::AssertEq( String::Pos( 'http://stepanov.lv', 'lv' ), 16 );

        // Latvian
        parent::AssertEq( String::Pos( 'Krīze nozīmē neskaidro procesu evolūciju,', 'lūc' ), 34 );
        parent::AssertEq( String::Pos( 'Ekonomiskā krīze ir nozīmīga ekonomikas cikla fāze.', 'īz' ), 13 );
        parent::AssertEq( String::Pos( 'Baltijas tīģeris', 'īģer' ), 10 );

        parent::AssertEq( String::Pos( 'Krīze nozīmē neskaidro procesu evolūciju,', 'lūc' ), 34 );
        parent::AssertEq( String::Pos( 'Ekonomīzkā krīze ir nozīmīga ekonomikas cikla fāze.', 'īz', 7 ), 13 );
        parent::AssertEq( String::Pos( 'Baīģeras tīģeris', 'īģer', 3 ), 10 );

        // Mixed
        parent::AssertEq( String::Pos( 'か櫧경ыDī', 'か' ), 0 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī', '櫧' ), 1 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī', '경' ), 2 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī', 'ы' ), 3 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī', 'D' ), 4 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī', 'ī' ), 5 );

        parent::AssertEq( String::Pos( 'か櫧경ыDīか', 'か', 1 ), 6 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī櫧', '櫧', 2 ), 6 );
        parent::AssertEq( String::Pos( 'か櫧경ыDī경', '경', 3 ), 6 );
        parent::AssertEq( String::Pos( 'か櫧경ыDīы', 'ы', 4 ), 6 );
        parent::AssertEq( String::Pos( 'か櫧경ыDīD', 'D', 5 ), 6 );
        parent::AssertEq( String::Pos( 'か櫧경ыDīī', 'ī', 6 ), 6 );
    }

    public function Lines()
    {
        // English
        parent::AssertEq( String::Lines( '' ), 1 );
        parent::AssertEq( String::Lines( 'Dmitry' ), 1 );

        parent::AssertEq( String::Lines( "\n" ), 2 );
        parent::AssertEq( String::Lines( "Dmitry\n" ), 2 );
        parent::AssertEq( String::Lines( "Dmitry\nStepanov" ), 2 );
        parent::AssertEq( String::Lines( "\nStepanov" ), 2 );

        parent::AssertEq( String::Lines( "\n\n" ), 3 );
        parent::AssertEq( String::Lines( "Dmitry\nDmitry\nStepanov" ), 3 );

        // Japanese
        parent::AssertEq( String::Lines( 'かゐ卧 禞わ覟鋨' ), 1 );
        parent::AssertEq( String::Lines( '䤣鰧げ 奞秤つむや 馤ちゅ, 榵䧦 驚鄢' ), 1 );
        parent::AssertEq( String::Lines( 'ェ すフィェ, 褚焩ウ 稞' ), 1 );

        parent::AssertEq( String::Lines( "かゐ卧 禞わ\n覟鋨" ), 2 );
        parent::AssertEq( String::Lines( "䤣鰧げ 奞秤\nつむや 馤ちゅ, 榵䧦 驚鄢" ), 2 );
        parent::AssertEq( String::Lines( "ェ すフィェ, 褚焩\nウ 稞" ), 2 );

        // Chinese
        parent::AssertEq( String::Lines( '絼綒 櫧櫋瀩, 膗 圛' ), 1 );
        parent::AssertEq( String::Lines( ' 孻憵懥 嬽巃攇 皾籈譧 眅' ), 1 );
        parent::AssertEq( String::Lines( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 墏 紽翍 ' ), 1 );

        parent::AssertEq( String::Lines( "絼綒 櫧櫋\n瀩, 膗 圛" ), 2 );
        parent::AssertEq( String::Lines( " 孻憵懥 嬽巃\n攇 皾籈譧 眅" ), 2 );
        parent::AssertEq( String::Lines( "鼲, 蓂蓌蓖 輲輹輴 誙\n 奿尕 墏 紽翍 " ), 2 );

        // Korean
        parent::AssertEq( String::Lines( '하는 아시아 여러 국가에서는 한국어를 가리' ), 1 );
        parent::AssertEq( String::Lines( ' 경향이 있기 때문에' ), 1 );
        parent::AssertEq( String::Lines( '슷하다. 현재는 한자를 사용하지 ' ), 1 );

        parent::AssertEq( String::Lines( "하는 아시아 여러 국가\n에서는 한국어를 가리" ), 2 );
        parent::AssertEq( String::Lines( " 경향이 있기 때문\n에" ), 2 );
        parent::AssertEq( String::Lines( "슷하다. 현\n재는 한자를 사용하지 " ), 2 );

        // Russian
        parent::AssertEq( String::Lines( 'Хаж ад унюм емпыдит вэртырэм' ), 1 );
        parent::AssertEq( String::Lines( 'нюлльам жэмпэр ыкжплььикари, мэя ад мёнём' ), 1 );
        parent::AssertEq( String::Lines( ' эи дуо. Квуй дэниквюы тхэопхр' ), 1 );

        parent::AssertEq( String::Lines( "Хаж ад унюм \nемпыдит вэртырэм" ), 2 );
        parent::AssertEq( String::Lines( "нюлльам жэмпэр ыкжпл\nььикари, мэя ад мёнём" ), 2 );
        parent::AssertEq( String::Lines( " эи дуо. К\nвуй дэниквюы тхэопхр" ), 2 );

        // Latvian
        parent::AssertEq( String::Lines( 'Krīze nozīmē neskaidro procesu evolūciju,' ), 1 );
        parent::AssertEq( String::Lines( 'Ekonomiskā krīze ir nozīmīga ekonomikas cikla fāze.' ), 1 );
        parent::AssertEq( String::Lines( 'Baltijas tīģeris' ), 1 );

        parent::AssertEq( String::Lines( "Krīze nozīmē neskaidro\n procesu evolūciju," ), 2 );
        parent::AssertEq( String::Lines( "Ekonomisk\nā krīze ir nozīmīga ekonomikas cikla fāze." ), 2 );
        parent::AssertEq( String::Lines( "Baltijas tīģ\neris" ), 2 );
    }

    public function SingleLine()
    {
        // English
        parent::AssertTrue( String::SingleLine( '' ) );
        parent::AssertTrue( String::SingleLine( 'Dmitry' ) );

        parent::AssertFalse( String::SingleLine( "\n" ) );
        parent::AssertFalse( String::SingleLine( "Dmitry\n" ) );
        parent::AssertFalse( String::SingleLine( "Dmitry\nStepanov" ) );
        parent::AssertFalse( String::SingleLine( "\nStepanov" ) );

        parent::AssertFalse( String::SingleLine( "\n\n" ) );
        parent::AssertFalse( String::SingleLine( "Dmitry\nDmitry\nStepanov" ) );

        // Japanese
        parent::AssertTrue( String::SingleLine( 'かゐ卧 禞わ覟鋨' ) );
        parent::AssertTrue( String::SingleLine( '䤣鰧げ 奞秤つむや 馤ちゅ, 榵䧦 驚鄢' ) );
        parent::AssertTrue( String::SingleLine( 'ェ すフィェ, 褚焩ウ 稞' ) );

        parent::AssertFalse( String::SingleLine( "かゐ卧 禞わ\n覟鋨" ) );
        parent::AssertFalse( String::SingleLine( "䤣鰧げ 奞秤\nつむや 馤ちゅ, 榵䧦 驚鄢" ) );
        parent::AssertFalse( String::SingleLine( "ェ すフィェ, 褚焩\nウ 稞" ) );

        // Chinese
        parent::AssertTrue( String::SingleLine( '絼綒 櫧櫋瀩, 膗 圛' ) );
        parent::AssertTrue( String::SingleLine( ' 孻憵懥 嬽巃攇 皾籈譧 眅' ) );
        parent::AssertTrue( String::SingleLine( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 墏 紽翍 ' ) );

        parent::AssertFalse( String::SingleLine( "絼綒 櫧櫋\n瀩, 膗 圛" ) );
        parent::AssertFalse( String::SingleLine( " 孻憵懥 嬽巃\n攇 皾籈譧 眅" ) );
        parent::AssertFalse( String::SingleLine( "鼲, 蓂蓌蓖 輲輹輴 誙\n 奿尕 墏 紽翍 " ) );

        // Korean
        parent::AssertTrue( String::SingleLine( '하는 아시아 여러 국가에서는 한국어를 가리' ) );
        parent::AssertTrue( String::SingleLine( ' 경향이 있기 때문에' ) );
        parent::AssertTrue( String::SingleLine( '슷하다. 현재는 한자를 사용하지 ' ) );

        parent::AssertFalse( String::SingleLine( "하는 아시아 여러 국가\n에서는 한국어를 가리" ) );
        parent::AssertFalse( String::SingleLine( " 경향이 있기 때문\n에" ) );
        parent::AssertFalse( String::SingleLine( "슷하다. 현\n재는 한자를 사용하지 " ) );

        // Russian
        parent::AssertTrue( String::SingleLine( 'Хаж ад унюм емпыдит вэртырэм' ) );
        parent::AssertTrue( String::SingleLine( 'нюлльам жэмпэр ыкжплььикари, мэя ад мёнём' ) );
        parent::AssertTrue( String::SingleLine( ' эи дуо. Квуй дэниквюы тхэопхр' ) );

        parent::AssertFalse( String::SingleLine( "Хаж ад унюм \nемпыдит вэртырэм" ) );
        parent::AssertFalse( String::SingleLine( "нюлльам жэмпэр ыкжпл\nььикари, мэя ад мёнём" ) );
        parent::AssertFalse( String::SingleLine( " эи дуо. К\nвуй дэниквюы тхэопхр" ) );

        // Latvian
        parent::AssertTrue( String::SingleLine( 'Krīze nozīmē neskaidro procesu evolūciju,' ) );
        parent::AssertTrue( String::SingleLine( 'Ekonomiskā krīze ir nozīmīga ekonomikas cikla fāze.' ) );
        parent::AssertTrue( String::SingleLine( 'Baltijas tīģeris' ) );

        parent::AssertFalse( String::SingleLine( "Krīze nozīmē neskaidro\n procesu evolūciju," ) );
        parent::AssertFalse( String::SingleLine( "Ekonomisk\nā krīze ir nozīmīga ekonomikas cikla fāze." ) );
        parent::AssertFalse( String::SingleLine( "Baltijas tīģ\neris" ) );
    }

    public function MultiLine()
    {
        // English
        parent::AssertFalse( String::MultiLine( '' ) );
        parent::AssertFalse( String::MultiLine( 'Dmitry' ) );

        parent::AssertTrue( String::MultiLine( "\n" ) );
        parent::AssertTrue( String::MultiLine( "Dmitry\n" ) );
        parent::AssertTrue( String::MultiLine( "Dmitry\nStepanov" ) );
        parent::AssertTrue( String::MultiLine( "\nStepanov" ) );

        parent::AssertTrue( String::MultiLine( "\n\n" ) );
        parent::AssertTrue( String::MultiLine( "Dmitry\nDmitry\nStepanov" ) );

        // Japanese
        parent::AssertFalse( String::MultiLine( 'かゐ卧 禞わ覟鋨' ) );
        parent::AssertFalse( String::MultiLine( '䤣鰧げ 奞秤つむや 馤ちゅ, 榵䧦 驚鄢' ) );
        parent::AssertFalse( String::MultiLine( 'ェ すフィェ, 褚焩ウ 稞' ) );

        parent::AssertTrue( String::MultiLine( "かゐ卧 禞わ\n覟鋨" ) );
        parent::AssertTrue( String::MultiLine( "䤣鰧げ 奞秤\nつむや 馤ちゅ, 榵䧦 驚鄢" ) );
        parent::AssertTrue( String::MultiLine( "ェ すフィェ, 褚焩\nウ 稞" ) );

        // Chinese
        parent::AssertFalse( String::MultiLine( '絼綒 櫧櫋瀩, 膗 圛' ) );
        parent::AssertFalse( String::MultiLine( ' 孻憵懥 嬽巃攇 皾籈譧 眅' ) );
        parent::AssertFalse( String::MultiLine( '鼲, 蓂蓌蓖 輲輹輴 誙 奿尕 墏 紽翍 ' ) );

        parent::AssertTrue( String::MultiLine( "絼綒 櫧櫋\n瀩, 膗 圛" ) );
        parent::AssertTrue( String::MultiLine( " 孻憵懥 嬽巃\n攇 皾籈譧 眅" ) );
        parent::AssertTrue( String::MultiLine( "鼲, 蓂蓌蓖 輲輹輴 誙\n 奿尕 墏 紽翍 " ) );

        // Korean
        parent::AssertFalse( String::MultiLine( '하는 아시아 여러 국가에서는 한국어를 가리' ) );
        parent::AssertFalse( String::MultiLine( ' 경향이 있기 때문에' ) );
        parent::AssertFalse( String::MultiLine( '슷하다. 현재는 한자를 사용하지 ' ) );

        parent::AssertTrue( String::MultiLine( "하는 아시아 여러 국가\n에서는 한국어를 가리" ) );
        parent::AssertTrue( String::MultiLine( " 경향이 있기 때문\n에" ) );
        parent::AssertTrue( String::MultiLine( "슷하다. 현\n재는 한자를 사용하지 " ) );

        // Russian
        parent::AssertFalse( String::MultiLine( 'Хаж ад унюм емпыдит вэртырэм' ) );
        parent::AssertFalse( String::MultiLine( 'нюлльам жэмпэр ыкжплььикари, мэя ад мёнём' ) );
        parent::AssertFalse( String::MultiLine( ' эи дуо. Квуй дэниквюы тхэопхр' ) );

        parent::AssertTrue( String::MultiLine( "Хаж ад унюм \nемпыдит вэртырэм" ) );
        parent::AssertTrue( String::MultiLine( "нюлльам жэмпэр ыкжпл\nььикари, мэя ад мёнём" ) );
        parent::AssertTrue( String::MultiLine( " эи дуо. К\nвуй дэниквюы тхэопхр" ) );

        // Latvian
        parent::AssertFalse( String::MultiLine( 'Krīze nozīmē neskaidro procesu evolūciju,' ) );
        parent::AssertFalse( String::MultiLine( 'Ekonomiskā krīze ir nozīmīga ekonomikas cikla fāze.' ) );
        parent::AssertFalse( String::MultiLine( 'Baltijas tīģeris' ) );

        parent::AssertTrue( String::MultiLine( "Krīze nozīmē neskaidro\n procesu evolūciju," ) );
        parent::AssertTrue( String::MultiLine( "Ekonomisk\nā krīze ir nozīmīga ekonomikas cikla fāze." ) );
        parent::AssertTrue( String::MultiLine( "Baltijas tīģ\neris" ) );
    }

    public function UpperCase()
    {
        parent::AssertTrue( false );
    }

    public function LowerCase()
    {
        parent::AssertTrue( false );
    }
}