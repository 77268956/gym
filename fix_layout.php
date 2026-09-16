<?php
$f = 'c:/laragon/www/GymX/resources/views/user.blade.php';
$c = file_get_contents($f);

// 1. Fix card colors (Make them all one color, like the primary gradient)
$c = preg_replace('/\.kpi-card-[1234] \{ [^\}]+\} /', '', $c); // Remove specific classes if they existed in regex? 
// The actual CSS is:
// .kpi-card-1 { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
// .kpi-card-2 { background: linear-gradient(135deg, #10B981, #059669); }
// .kpi-card-3 { background: linear-gradient(135deg, #F59E0B, #D97706); }
// .kpi-card-4 { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
$cssToReplace = <<<EOD
    .kpi-card-1 { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
    .kpi-card-2 { background: linear-gradient(135deg, #10B981, #059669); }
    .kpi-card-3 { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .kpi-card-4 { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
EOD;

$cssNew = <<<EOD
    .kpi-card-1, .kpi-card-2, .kpi-card-3, .kpi-card-4 { 
        background: linear-gradient(135deg, #1E293B, #0F172A); 
    }
EOD;
$c = str_replace($cssToReplace, $cssNew, $c);

// 2. Fix the split table
// The problem is that table-panel is `overflow-y: auto`, AND DataTables has `scrollY`. They conflict.
// If DataTables handles the scrolling, `table-panel` shouldn't.
// Let's change `scrollY: "calc(100vh - 280px)"` to calculate dynamically or just use standard css flexbox on the datatables body!
$jsToReplace = <<<EOD
        scrollY: "calc(100vh - 280px)",
        scrollCollapse: true,
EOD;
$jsNew = <<<EOD
        scrollY: (window.innerHeight - 380) + 'px',
        scrollCollapse: true,
EOD;
$c = str_replace($jsToReplace, $jsNew, $c);

file_put_contents($f, $c);
echo "Fixed table and colors.";