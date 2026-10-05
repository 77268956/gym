@extends(auth()->check() ? 'layouts.app' : 'layouts.scanner')

@section('title', 'Escanear Asistencia - Empleados')

@section('skeleton')
    <div style="max-width: 760px; margin: 0 auto;">
        <div class="skel-box" style="height: 32px; width: 300px; margin: 0 auto 1.5rem auto;"></div>
        <div class="skel-box" style="height: 480px; width: 100%; border-radius: 12px; margin-bottom: 1rem;"></div>
    </div>
@endsection

@push('styles')
<style>
    .scanner-container {
        position: relative;
        width: 100%;
        max-width: 560px;
        aspect-ratio: 4 / 3;
        margin: 0 auto;
        border-radius: 6px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .scanner-page { max-width: 640px; margin: 0 auto; padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .scanner-card, .result-card { background: #fff; border: 0; border-radius: 6px; box-shadow: 0 6px 18px rgba(15, 23, 42, .08); overflow: hidden; }
    .scanner-card .card-header, .scanner-card .card-footer { background: #fff; border: 0; }
    .scan-clock { color: var(--sidebar-bg); font-size: 1.35rem; font-weight: 700; text-align: center; letter-spacing: .02em; }
    .scan-date { color: #64748B; font-size: .85rem; text-align: center; text-transform: capitalize; }
    .result-card { max-width: 480px; margin: 0 auto; padding: 1.5rem; text-align: center; border-top: 4px solid var(--primary); }
    .result-visual { min-height: 96px; display: flex; align-items: center; justify-content: center; gap: .75rem; margin-bottom: .75rem; }
    .result-icon { width: 64px; height: 64px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: .75rem; }
    .result-icon.success { background: #D1FAE5; color: #059669; }
    .result-icon.error { background: #FEE2E2; color: #DC2626; }
    .result-icon.warning { background: #FEF3C7; color: #D97706; }
    .result-photo { width: 96px; height: 96px; object-fit: cover; border-radius: 6px; border: 3px solid var(--primary); margin-bottom: .75rem; }
    .result-visual .result-icon, .result-visual .result-photo { margin-bottom: 0; }
    .result-info { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: .7rem; }
    .result-card #employeeDetails { margin-left: 0; margin-right: 0; }
    .result-card #employeeDetails > [class*="col-"] { padding-left: .4rem; padding-right: .4rem; }
    .result-info-label { color: #64748B; font-size: .72rem; text-transform: uppercase; font-weight: 600; }
    .result-info-value { color: var(--sidebar-bg); font-size: 1rem; font-weight: 700; }
    .tipo-entrada  { background: #D1FAE5; color: #059669; padding: .25rem .75rem; border-radius: 50px; font-size: .8rem; font-weight: 700; }
    .tipo-salida   { background: #DBEAFE; color: #1D4ED8; padding: .25rem .75rem; border-radius: 50px; font-size: .8rem; font-weight: 700; }
    .badge-tardanza { background: #FEF3C7; color: #D97706; padding: .2rem .6rem; border-radius: 50px; font-size: .7rem; font-weight: 700; }
    @media (max-width: 767.98px) { .scanner-page { padding: 0 .5rem; } .result-card { padding: 1.5rem 1rem; } }
    #webcamVideo { width: 100%; height: auto; display: block; }
    #overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 scanner-page">

    {{-- Scanner Screen --}}
    <div id="scannerScreen">
        <div class="scanner-card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-user-tie mr-2"></i> Escáner de Empleados
                </h5>
                <span id="modelLoader" class="badge badge-warning p-2">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Cargando modelos AI...
                </span>
            </div>
            <div class="card-body p-0 bg-dark text-center position-relative">
                <div class="scanner-container">
                    <video id="webcamVideo" autoplay muted playsinline></video>
                    <canvas id="overlay"></canvas>
                </div>
            </div>
            <div class="py-2">
                <div id="scanClock" class="scan-clock">--:--:--</div>
                <div id="scanDate" class="scan-date">Cargando fecha...</div>
            </div>
            <div class="card-footer bg-white text-center">
                <button class="btn btn-primary font-weight-bold px-4" id="btnStart" disabled onclick="startScanning()">
                    <i class="fas fa-play mr-2"></i> Iniciar Escáner
                </button>
                <button class="btn btn-danger font-weight-bold px-4 d-none" id="btnStop" onclick="stopScanning()">
                    <i class="fas fa-stop mr-2"></i> Detener
                </button>
            </div>
        </div>
    </div>

    {{-- Result Screen --}}
    <div id="resultScreen" class="d-none">
        <div class="result-card">
            <div class="result-visual">
                <div id="resultIcon" class="result-icon success"><i class="fas fa-check"></i></div>
                <img id="employeeFoto" src="" class="result-photo d-none" alt="Foto del empleado">
            </div>
            <h3 id="resultTitle" class="font-weight-bold mb-1">Empleado reconocido</h3>
            <p id="resultMessage" class="text-muted mb-3">Asistencia registrada correctamente.</p>

            <div id="tipoBadge" class="mb-3"></div>

            <div id="employeeDetails" class="row text-left mb-4">
                <div class="col-6 mb-3">
                    <div class="result-info">
                        <span class="result-info-label d-block">Empleado</span>
                        <span id="employeeNombre" class="result-info-value">—</span>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="result-info">
                        <span class="result-info-label d-block">Hora</span>
                        <span id="employeeHora" class="result-info-value">—</span>
                    </div>
                </div>
                <div class="col-12" id="tardanzaRow" style="display:none;">
                    <div class="alert alert-warning py-2 mb-0 text-center">
                        <i class="fas fa-clock mr-1"></i> <strong>Registro con tardanza</strong>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="volverAEscanear()">
                <i class="fas fa-camera mr-2"></i> Escanear otro empleado
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    const video   = document.getElementById('webcamVideo');
    const overlay = document.getElementById('overlay');

    let faceMatcher   = null;
    let isScanning    = false;
    let scanInterval  = null;
    let lastScannedId = null;
    let resultTimer   = null;
    let unknownFrames  = 0;

    // Empleados cargados desde la BD
    const dbEmpleados = @json($empleados);

    // Endpoint de registro
    const registrarUrl = '{{ request()->routeIs('asistencias_empleados.publico') ? route('asistencias_empleados.publico.registrar') : route('asistencias_empleados.registrar') }}';

    async function loadModelsAndData() {
        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('{{ asset('models') }}'),
                faceapi.nets.faceLandmark68Net.loadFromUri('{{ asset('models') }}'),
                faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset('models') }}'),
            ]);

            const labeledDescriptors = [];
            for (let emp of dbEmpleados) {
                if (emp.descriptor_facial) {
                    try {
                        let parsed = typeof emp.descriptor_facial === 'string'
                            ? JSON.parse(emp.descriptor_facial)
                            : emp.descriptor_facial;
                        let rawArray = Array.isArray(parsed) ? parsed : Object.values(parsed);
                        if (rawArray.length === 128) {
                            labeledDescriptors.push(
                                new faceapi.LabeledFaceDescriptors(emp.id.toString(), [new Float32Array(rawArray)])
                            );
                        }
                    } catch (e) {
                        console.error('Error descriptor empleado ' + emp.id, e);
                    }
                }
            }

            if (labeledDescriptors.length > 0) {
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.65);
            } else {
                throw new Error('No hay empleados activos con descriptor facial registrado.');
            }

            document.getElementById('modelLoader').className = 'badge badge-success p-2';
            document.getElementById('modelLoader').innerHTML = '<i class="fas fa-check-circle mr-1"></i> Listo';
            document.getElementById('btnStart').disabled = false;

        } catch (e) {
            console.error(e);
            document.getElementById('modelLoader').className = 'badge badge-danger p-2';
            document.getElementById('modelLoader').innerHTML = '<i class="fas fa-times-circle mr-1"></i> ' + (e.message || 'Error al cargar');
        }
    }

    async function startScanning() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = stream;
            document.getElementById('btnStart').classList.add('d-none');
            document.getElementById('btnStop').classList.remove('d-none');
            isScanning = true;
        } catch (err) {
            alert('No se puede acceder a la cámara.');
        }
    }

    function stopScanning() {
        if (video.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
        document.getElementById('btnStart').classList.remove('d-none');
        document.getElementById('btnStop').classList.add('d-none');
        isScanning = false;
        clearInterval(scanInterval);
        overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
    }

    video.addEventListener('play', () => {
        const width  = video.videoWidth  || 640;
        const height = video.videoHeight || 480;
        overlay.width  = width;
        overlay.height = height;
        const displaySize = { width, height };
        faceapi.matchDimensions(overlay, displaySize);

        scanInterval = setInterval(async () => {
            if (!isScanning || !faceMatcher) return;

            const detections = await faceapi
                .detectAllFaces(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.4 }))
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resized = faceapi.resizeResults(detections, displaySize);
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);
            faceapi.draw.drawDetections(overlay, resized);

            const results = resized.map(d => faceMatcher.findBestMatch(d.descriptor));
            results.forEach((result, i) => {
                const box = resized[i].detection.box;
                new faceapi.draw.DrawBox(box, { label: result.toString() }).draw(overlay);

                if (result.label !== 'unknown' && result.label !== lastScannedId) {
                    unknownFrames = 0;
                    procesarAsistencia(result.label);
                } else if (result.label === 'unknown' && lastScannedId === null) {
                    unknownFrames++;
                }

                if (unknownFrames >= 3 && lastScannedId === null) {
                    mostrarNoReconocido();
                }
            });
        }, 1000);
    });

    function procesarAsistencia(empleadoId) {
        lastScannedId = empleadoId;

        fetch(registrarUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ empleado_id: empleadoId }),
        })
        .then(async response => {
            const responseText = await response.text();
            let data;

            try {
                data = JSON.parse(responseText);
            } catch (error) {
                throw new Error('El servidor no devolvió una respuesta JSON válida. Verifica la sesión y los permisos del escáner.');
            }

            if (!response.ok) {
                throw new Error(data.message || data.error || 'No se pudo registrar la asistencia.');
            }

            return data;
        })
        .then(data => {
            detenerCamara();
            mostrarResultado(data.status, data);
            resultTimer = setTimeout(volverAEscanear, 6000);
        })
        .catch(err => {
            console.error(err);
            detenerCamara();
            mostrarResultado('error', { message: err.message || 'No se pudo registrar la asistencia.' });
            resultTimer = setTimeout(volverAEscanear, 6000);
        });
    }

    function mostrarResultado(status, data) {
        document.getElementById('scannerScreen').classList.add('d-none');
        document.getElementById('resultScreen').classList.remove('d-none');

        const iconEl  = document.getElementById('resultIcon');
        const fotoEl  = document.getElementById('employeeFoto');
        const details = document.getElementById('employeeDetails');
        const badge   = document.getElementById('tipoBadge');
        const tardRow = document.getElementById('tardanzaRow');

        // Icono según estado
        const iconMap = {
            success: { cls: 'success', icon: 'check' },
            warning: { cls: 'warning', icon: 'exclamation' },
            error:   { cls: 'error',   icon: 'times' },
        };
        const ic = iconMap[status] || iconMap.error;
        iconEl.className = 'result-icon ' + ic.cls;
        iconEl.innerHTML = `<i class="fas fa-${ic.icon}"></i>`;

        const esValido = status === 'success';
        document.getElementById('resultTitle').textContent   = data.empleado ? (esValido ? '¡Hola, ' + data.empleado + '!' : data.empleado) : 'No reconocido';
        document.getElementById('resultMessage').textContent = data.message || '';

        if (esValido && data.empleado) {
            details.classList.remove('d-none');
            document.getElementById('employeeNombre').textContent = data.empleado;
            document.getElementById('employeeHora').textContent   = data.hora || '—';

            // Badge de tipo
            if (data.tipo_registro === 'entrada') {
                badge.innerHTML = '<span class="tipo-entrada"><i class="fas fa-sign-in-alt mr-1"></i>Entrada</span>';
            } else if (data.tipo_registro === 'salida') {
                badge.innerHTML = '<span class="tipo-salida"><i class="fas fa-sign-out-alt mr-1"></i>Salida</span>';
            } else {
                badge.innerHTML = '';
            }

            // Tardanza
            tardRow.style.display = data.tardanza ? 'block' : 'none';

            // Foto
            fotoEl.src = data.foto || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.empleado)}&background=2563EB&color=fff`;
            fotoEl.classList.remove('d-none');
            iconEl.classList.add('d-none');
        } else {
            details.classList.add('d-none');
            fotoEl.classList.add('d-none');
            badge.innerHTML = '';
        }
    }

    function mostrarNoReconocido() {
        lastScannedId = 'unknown';
        detenerCamara();
        mostrarResultado('error', { message: 'El rostro no coincide con ningún empleado registrado.' });
    }

    function detenerCamara() {
        if (video.srcObject) { video.srcObject.getTracks().forEach(t => t.stop()); video.srcObject = null; }
        isScanning = false;
        clearInterval(scanInterval);
        overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
    }

    function volverAEscanear() {
        clearTimeout(resultTimer);
        lastScannedId = null;
        unknownFrames = 0;
        document.getElementById('resultScreen').classList.add('d-none');
        document.getElementById('scannerScreen').classList.remove('d-none');
        document.getElementById('btnStart').classList.remove('d-none');
        document.getElementById('btnStop').classList.add('d-none');
    }

    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('scanClock').textContent = ahora.toLocaleTimeString('es-HN');
        document.getElementById('scanDate').textContent  = ahora.toLocaleDateString('es-HN', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadModelsAndData();
        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    });
</script>
@endpush
