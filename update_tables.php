<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$iter = new RecursiveIteratorIterator($dir);
foreach ($iter as $file) {
    if (strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getPathname());
        
        $oldTop = "\"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>\"";
        $newTop = "\"<'row mb-2'<'col-sm-12 text-right'f>>\"";
        $content = str_replace($oldTop, $newTop, $content);
        
        $oldBot = "\"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>\"";
        $newBot = "\"<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>\"";
        $content = str_replace($oldBot, $newBot, $content);
        
        file_put_contents($file->getPathname(), $content);
    }
}
echo "Done.\n";
