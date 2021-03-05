<?php

function dump($variable, ...$variables)
{
    $data = [$variable];

    if (count($variables)) {
        foreach ($variables as $variable) {
            $data[] = $variable;
        }
    }

    foreach ($data as $key => $variable) {
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

function deleteTwigCacheFolder()
{
    $dir = dirname(__DIR__, 2).'\var\\cache\\twig';
    if (is_dir($dir)) {
        deleteDir($dir);
    }
}

function deleteDir($dirPath)
{
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

deleteTwigCacheFolder();
