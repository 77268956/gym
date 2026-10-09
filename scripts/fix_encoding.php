<?php
$dir = new RecursiveDirectoryIterator('c:/laragon/www/GymX/resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        // Reverse UTF8 double encoding
        $fixed = utf8_decode($content);
        file_put_contents($path, $fixed);
        echo "Fixed: $path\n";
    }
}