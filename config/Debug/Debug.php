<?php

/**
 * @param mixed $variable
 * @param mixed $variables
 */
function dump($variable, ...$variables): void
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

/**
 * @param mixed $variable
 * @param mixed $variables
 */
function dd($variable, ...$variables): void
{
    dump($variable, ...$variables);

    die();
}

function deleteTwigCacheFolder(): void
{
    $dir = dirname(__DIR__, 2).'\var\\cache\\twig';
    if (is_dir($dir)) {
        deleteDir($dir);
    }
}

function deleteDir(string $dirPath): void
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

//deleteTwigCacheFolder();
