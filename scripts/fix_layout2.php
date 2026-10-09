<?php
$f = 'c:/laragon/www/GymX/resources/views/user.blade.php';
$c = file_get_contents($f);

// Make the ic-card holding the table a flex column
$c = str_replace('<div class="ic-card h-100">', '<div class="ic-card h-100 d-flex flex-column">', $c);

// Make the table-panel fill the remaining space of the card
$c = str_replace('<div class="table-panel">', '<div class="table-panel flex-grow-1" style="min-height:0;">', $c);

// Remove the hardcoded JS scrollY from DataTables so it just expands normally inside the scrolling table-panel
$jsOld = <<<EOD
        scrollY: (window.innerHeight - 380) + 'px',
        scrollCollapse: true,
EOD;
$c = str_replace($jsOld, "", $c);

// Wait, DataTables creates its own wrappers (div.dataTables_wrapper). If we don't use scrollY, the table itself doesn't scroll inside the wrapper, the whole wrapper scrolls. That's actually fine, but the header will scroll away.
// If the user wants the header fixed, it's better to use DataTables `scrollY: "50vh"` for example.
// Let's just fix the CSS for `dataTables_wrapper` to fill height.
$cssAdd = <<<EOD
    /* Fix datatables height */
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }
EOD;

$c = str_replace('.dataTables_wrapper .row { margin-left: 0; margin-right: 0; }', '.dataTables_wrapper .row { margin-left: 0; margin-right: 0; }' . "\n" . $cssAdd, $c);

// We need scrollY: '100%' in DataTables for it to generate the scrollBody wrapper
$c = preg_replace('/pageLength: 25,/', "pageLength: 25,\n        scrollY: '100%',\n        scrollCollapse: true,", $c);

file_put_contents($f, $c);
echo "Applied real flexbox height to DataTables.";