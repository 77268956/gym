<?php
$f = 'c:/laragon/www/GymX/resources/views/user.blade.php';
$c = file_get_contents($f);

// Remove links
$c = preg_replace('/\{\{ \$clientes->links.*\}\}/', '', $c);

// Remove total()
$c = str_replace('de {{ $clientes->total() }} clientes', '', $c);

// Add DataTables script
$script = <<<'EOD'
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('table.ic-table').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 10
    });
});
</script>
EOD;

$c = str_replace('</body>', $script . "\n</body>", $c);
// wait, user.blade.php probably extends app.blade.php and has @push('scripts')
if (strpos($c, "@push('scripts')") !== false) {
    $c = preg_replace("/@push\('scripts'\)/", "@push('scripts')\n" . $script, $c);
} else {
    $c .= "\n@push('scripts')\n$script\n@endpush\n";
}

file_put_contents($f, $c);
echo "Fixed user.blade.php\n";