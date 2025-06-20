<?php

namespace App\Types;

enum Tag: string
{
    // styles.xml
    case styles = "styles";
    case docDefaults = "docDefaults";
    case style = "style";
    case basedOn = "basedOn";
    case name = "name";

    // document.xml
    case document = "document";
    case body = "body";
    case p = "p";
    case sdt = "sdt";
    case sdtPr = "sdtPr";
    case sdtContent = "sdtContent";
    case hyperlink = "hyperlink";
    case r = "r";
    case t = "t";
    case tbl = "tbl";
    case tr = "tr";
    case trPr = "trPr";
    case tc = "tc";
    case tblHeader = "tblHeader";

    // rPr
    case rPr = "rPr";
    case rFonts = "rFonts";
    case sz = "sz";
    case szCs = "szCs";
    case i = "i";
    case b = "b";

    // pPr
    case pPr = "pPr";
    case pStyle = "pStyle";
    case jc = "jc";
    case spacing = "spacing";
}
