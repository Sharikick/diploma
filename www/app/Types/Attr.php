<?php

namespace App\Types;

enum Attr: string {
    case val = "val";
    case line = "line";
    case lineRule = "lineRule";
    case ascii = "ascii";
    case styleId = "styleId";
}
