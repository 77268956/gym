<?php
// 1. Update app.blade.php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Make sure Cleave.js is included
if (strpos($c, 'cleave.min.js') === false) {
    $c = str_replace('<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>',
                     "<script src=\"https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js\"></script>\n    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js\"></script>", $c);
}

// Add the global JS for Notyf and Swal2
$globalJs = <<<'EOD'
    <script>
        // Configurar Notyf
        var notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            ripple: true
        });

        @if(session('success'))
            notyf.success("{{ session('success') }}");
        @endif
        
        @if(session('error'))
            notyf.error("{{ session('error') }}");
        @endif
        
        @if($errors->any())
            notyf.error("Por favor revisa los errores en el formulario.");
        @endif

        // SweetAlert2 global para formularios de eliminación
        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    @stack('scripts')
EOD;

$c = preg_replace('/@stack\(\'scripts\'\)/is', $globalJs, $c);
file_put_contents($f, $c);


// 2. Replace standard confirms with SweetAlert class
$viewsToFix = [
    'c:/laragon/www/GymX/resources/views/user.blade.php',
    'c:/laragon/www/GymX/resources/views/empleados/index.blade.php',
    'c:/laragon/www/GymX/resources/views/membresias/index.blade.php'
];

foreach ($viewsToFix as $view) {
    if (file_exists($view)) {
        $vc = file_get_contents($view);
        // Replace onsubmit="return confirm(...);" with class="d-inline form-delete"
        $vc = preg_replace('/class="d-inline"\s+onsubmit="return confirm\([^)]+\);"/i', 'class="d-inline form-delete"', $vc);
        $vc = preg_replace('/onsubmit="return confirm\([^)]+\);"\s+class="d-inline"/i', 'class="d-inline form-delete"', $vc);
        file_put_contents($view, $vc);
    }
}


// 3. Add Cleave.js to Clientes forms
function addCleaveToForm($file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        $cleaveScript = <<<'EOD'
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('telefono')) {
        new Cleave('#telefono', {
            delimiters: ['-'],
            blocks: [4, 4],
            numericOnly: true
        });
    }
    if (document.getElementById('cedula')) {
        new Cleave('#cedula', {
            delimiters: ['-', '-'],
            blocks: [4, 4, 5],
            numericOnly: true
        });
    }
});
</script>
EOD;
        if (strpos($c, 'Cleave(') === false) {
            $c = preg_replace('/@endpush/i', $cleaveScript . "\n@endpush", $c);
            file_put_contents($file, $c);
        }
    }
}

addCleaveToForm('c:/laragon/www/GymX/resources/views/clientes/create.blade.php');
addCleaveToForm('c:/laragon/www/GymX/resources/views/clientes/edit.blade.php');

echo "Libraries applied.\n";