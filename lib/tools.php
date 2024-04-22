<?php

function arrayDepth($array) 
{
    $max_depth = 1;

    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = arrayDepth($value) + 1;
            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}

function arraySearch(string $needle, array $haystack) 
{
    $depth = arrayDepth($haystack);

    if ($depth <= 1)
        return array_search($needle, $haystack);

    foreach($haystack as $key => $value) 
        return arraySearch($needle, $value);
}