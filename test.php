<?php

function check($str) {
    $stack = [];

    $chars = str_split($str);

    foreach ($chars as $char) {

        switch($char) {

            case '(':
            case '[':
                $stack[] = $char;
                break;

            case ']':
                if(empty($stack) || array_pop($stack) != '[')
                    return false;
                break;
            case ')':
                if(empty($stack) || array_pop($stack) != '(')
                    return false;
                break;

        }

    }
    return empty($stack);
}


$result = check('[()])');

var_dump($result);
