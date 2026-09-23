@extends(auth()->check() ? 'layouts.app' : 'layouts.scanner')

@section('title', 'Escanear Asistencia Facial')

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
    .result-photo { width: 96px; height: 96px; object-fit: cover; border-radius: 6px; border: 3px solid var(--primary); margin-bottom: .75rem; }
    .result-visual .result-icon, .result-visual .result-photo { margin-bottom: 0; }
    .result-info { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: .7rem; }
    .result-card #clientDetails { margin-left: 0; margin-right: 0; }
    .result-card #clientDetails > [class*="col-"] { padding-left: .4rem; padding-right: .4rem; }
    .result-info-label { color: #64748B; font-size: .72rem; text-transform: uppercase; font-weight: 600; }
    .result-info-value { color: var(--sidebar-bg); font-size: 1rem; font-weight: 700; }
    @media (max-width: 767.98px) { .scanner-page { padding: 0 .5rem; } .result-card { padding: 1.5rem 1rem; } }
    #webcamVideo {
        width: 100%;
        height: auto;
        display: block;
    }
    #overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
    }
    .status-panel {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        height: 100%;
    }
    .scan-status {
        text-align: center;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .scan-status.idle { background: #F1F5F9; color: #64748B; }
    .scan-status.success { background: #D1FAE5; color: #059669; }
    .scan-status.error { background: #FEE2E2; color: #DC2626; }
    .scan-status.warning { background: #FEF3C7; color: #D97706; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 scanner-page">
    <div id="scannerScreen">
        <div class="scanner-card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary"><i class="fas fa-camera mr-2"></i> Cámara de Recepción</h5>
                    <span id="modelLoader" class="badge badge-warning p-2"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando modelos AI...</span>
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

    <div id="resultScreen" class="d-none">
        <div class="result-card">
            <div class="result-visual">
                <div id="resultIcon" class="result-icon success"><i class="fas fa-check"></i></div>
                <img id="clientFoto" src="" class="result-photo d-none" alt="Foto del cliente">
            </div>
            <h3 id="resultTitle" class="font-weight-bold mb-2">Cliente reconocido</h3>
            <p id="resultMessage" class="text-muted mb-4">Asistencia registrada correctamente.</p>

            <div id="clientDetails" class="row text-left mb-4">
                <div class="col-6 mb-3">
                    <span class="result-info-label d-block">Cliente</span>
                    <span id="clientNombre" class="result-info-value">Nombre</span>
                </div>
                <div class="col-6 mb-3">
                    <span class="result-info-label d-block">Membresía</span>
                    <span id="clientMembresia" class="result-info-value">Vigente</span>
                </div>
                <div class="col-12">
                    <div class="result-info text-center">
                        <span class="result-info-label d-block">Puntos</span>
                        <span id="clientPuntos" class="result-info-value">0</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="volverAEscanear()">
                <i class="fas fa-camera mr-2"></i> Escanear otro cliente
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    const video = document.getElementById('webcamVideo');
    const overlay = document.getElementById('overlay');
    
    let faceMatcher = null;
    let isScanning = false;
    let scanInterval = null;
    let lastScannedId = null;
    let resultTimer = null;
    
    // Array de clientes de la base de datos
    const dbClientes = @json($clientes);

async function loadModelsAndData() {
        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models')
            ]);
            
            // Cargar los descriptores
            const labeledDescriptors = [];
            for (let c of dbClientes) {
                if (c.descriptor_facial) {
                    try {
                        let parsed = typeof c.descriptor_facial === 'string'
                            ? JSON.parse(c.descriptor_facial)
                            : c.descriptor_facial;

                        // Extraer los valores sea un Array o un Objeto indexado
                        let rawArray = Array.isArray(parsed) ? parsed : Object.values(parsed);

                        if (rawArray.length === 128) {
                            const descriptorArray = new Float32Array(rawArray);
                            labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(c.id.toString(), [descriptorArray]));
                        }
                    } catch (e) {
                        console.error("Error parseando descriptor de cliente " + c.id, e);
                    }
                }
            }

            if (labeledDescriptors.length > 0) {
                // Permite variaciones normales entre la foto registrada y la cámara.
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.58);
            }

            document.getElementById('modelLoader').className = 'badge badge-success p-2';
            document.getElementById('modelLoader').innerHTML = '<i class="fas fa-check-circle mr-1"></i> Listo';
            document.getElementById('btnStart').disabled = false;
            
        } catch (e) {
            console.error(e);
            document.getElementById('modelLoader').className = 'badge badge-danger p-2';
            document.getElementById('modelLoader').innerHTML = '<i class="fas fa-times-circle mr-1"></i> Error al cargar modelos';
        }
    }

    async function startScanning() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = stream;
            
            document.getElementById('btnStart').classList.add('d-none');
            document.getElementById('btnStop').classList.remove('d-none');
            isScanning = true;
            
        } catch(err) {
            alert("No se puede acceder a la cámara.");
        }
    }

    function stopScanning() {
        if(video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
        document.getElementById('btnStart').classList.remove('d-none');
        document.getElementById('btnStop').classList.add('d-none');
        isScanning = false;
        clearInterval(scanInterval);
        overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
    }

video.addEventListener('play', () => {
    // Asegurar que el video tenga dimensiones antes de dimensionar el canvas
    const width = video.videoWidth || 640;
    const height = video.videoHeight || 480;

    overlay.width = width;
    overlay.height = height;

    const displaySize = { width: width, height: height };
    faceapi.matchDimensions(overlay, displaySize);

    scanInterval = setInterval(async () => {
        if (!isScanning || !faceMatcher) return;

        // Detección
        const detections = await faceapi.detectAllFaces(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks()
            .withFaceDescriptors();

        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        const ctx = overlay.getContext('2d');
        ctx.clearRect(0, 0, overlay.width, overlay.height);

        faceapi.draw.drawDetections(overlay, resizedDetections);

        const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
        
        results.forEach((result, i) => {
            const box = resizedDetections[i].detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
            drawBox.draw(overlay);
            
            if (result.label !== 'unknown' && result.label !== lastScannedId) {
                procesarAsistencia(result.label);
            } else if (result.label === 'unknown' && lastScannedId === null) {
                mostrarClienteNoReconocido();
            }
        });
    }, 1000);
});

    function procesarAsistencia(clienteId) {
        lastScannedId = clienteId; // Bloqueo temporal
        
        // Enviar AJAX al backend
        fetch('{{ request()->routeIs('asistencias.publico') ? route('asistencias.publico.registrar') : route('asistencias.registrar') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cliente_id: clienteId })
        })
        .then(response => response.json())
        .then(data => {
            detenerCamara();
            mostrarResultado(data.status === 'success', data);

            resultTimer = setTimeout(() => {
                volverAEscanear();
            }, 5000);
        })
        .catch(err => {
            console.error(err);
            lastScannedId = null;
        });
    }

    function mostrarResultado(esValido, data) {
        const resultIcon = document.getElementById('resultIcon');
        const clientFoto = document.getElementById('clientFoto');
        const details = document.getElementById('clientDetails');

        document.getElementById('scannerScreen').classList.add('d-none');
        document.getElementById('resultScreen').classList.remove('d-none');
        resultIcon.className = 'result-icon ' + (esValido ? 'success' : 'error');
        resultIcon.classList.toggle('d-none', esValido);
        resultIcon.innerHTML = '<i class="fas fa-' + (esValido ? 'check' : 'times') + '"></i>';
        document.getElementById('resultTitle').textContent = esValido ? '¡Bienvenido, ' + data.cliente + '!' : 'Acceso no válido';
        document.getElementById('resultMessage').textContent = data.message || 'No se pudo validar la asistencia.';

        if (esValido) {
            details.classList.remove('d-none');
                document.getElementById('clientNombre').textContent = data.cliente;
                document.getElementById('clientMembresia').textContent = data.membresia_vence
                    ? 'Vence: ' + data.membresia_vence
                    : 'Sin membresía activa';
                document.getElementById('clientPuntos').textContent = data.puntos ?? 0;
            clientFoto.src = data.foto || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.cliente) + '&background=2563EB&color=fff';
            clientFoto.classList.remove('d-none');
        } else {
            details.classList.add('d-none');
            clientFoto.classList.add('d-none');
        }
    }

    function mostrarClienteNoReconocido() {
        lastScannedId = 'unknown';
        detenerCamara();
        mostrarResultado(false, {
            message: 'El rostro no coincide con ningún cliente registrado.'
        });
    }

    function detenerCamara() {
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
        isScanning = false;
        clearInterval(scanInterval);
        overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
    }

    function volverAEscanear() {
        clearTimeout(resultTimer);
        lastScannedId = null;
        document.getElementById('resultScreen').classList.add('d-none');
        document.getElementById('scannerScreen').classList.remove('d-none');
        document.getElementById('btnStart').classList.remove('d-none');
        document.getElementById('btnStop').classList.add('d-none');
    }

    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('scanClock').textContent = ahora.toLocaleTimeString('es-HN');
        document.getElementById('scanDate').textContent = ahora.toLocaleDateString('es-HN', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', () => {
        loadModelsAndData();
        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    });
</script>
@endpush