<?php

function dump($variable, ...$variables) {
    $data = [$variable];

    if(count($variables)) {
        foreach($variables as $variable) {
            $data[] = $variable;
        }            
    }

    foreach($data as $key => $variable) {
        echo '<pre>#'.$key;
        highlight_string("<?=\n" . var_export($variable, true) . ";\n?>");
        echo '</pre>';
    }
}

function dd($variable, ...$variables)
{
    dump($variable, ...$variables);

    die();
}