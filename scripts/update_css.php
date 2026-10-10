<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$iter = new RecursiveIteratorIterator($dir);
foreach ($iter as $file) {
    if (strpos($file->getFilename(), '.blade.php') !== false) {
        $content = file_get_contents($file->getPathname());

        // Replace CSS
        $oldTh = '.ic-table thead th { font-size: 0.7rem; color: var(--ic-muted); background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.4rem 0.5rem; }';
        $newTh = '.ic-table thead th { font-size: 0.75rem; font-weight: 700; color: #4e73df; background: #eaecf4; border-bottom: 2px solid #4e73df; padding: 0.75rem 0.5rem; letter-spacing: 0.5px; text-transform: uppercase; }';
        $content = str_replace($oldTh, $newTh, $content);

        $oldTd = '.ic-table td { font-size: 0.8rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; padding: 0.4rem 0.5rem; }';
        $newTd = '.ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }';
        $content = str_replace($oldTd, $newTd, $content);

        file_put_contents($file->getPathname(), $content);
    }
}
echo "CSS Updated.\n";
