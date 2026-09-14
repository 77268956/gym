<?php
$content = <<<'EOD'
@extends('layouts.app')

@section('title', 'Escanear Asistencia Facial')

@push('styles')
<style>
    .scanner-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
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
<div class="container-fluid py-4">
    
    <div class="row">
        <!-- Columna del escáner -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
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

        <!-- Columna de estado / resultado -->
        <div class="col-lg-5 mb-4">
            <div class="status-panel">
                <h5 class="font-weight-bold mb-4 text-center">Resultado del Escaneo</h5>
                
                <div id="statusBox" class="scan-status idle">
                    <i id="statusIcon" class="fas fa-user-clock fa-4x mb-3"></i>
                    <h4 id="statusTitle" class="font-weight-bold">Esperando cliente...</h4>
                    <p id="statusMsg" class="mb-0">Acércate a la cámara para registrar tu asistencia.</p>
                </div>

                <div id="clientDetails" class="d-none mt-4 text-center">
                    <img id="clientFoto" src="" class="rounded-circle shadow-sm mb-3" style="width:120px;height:120px;object-fit:cover;border:4px solid #fff;">
                    <h5 id="clientNombre" class="font-weight-bold text-dark mb-1">Nombre</h5>
                    <span id="clientMembresia" class="badge badge-primary px-3 py-2 mb-3">Vence: 12/12/2026</span>
                    
                    <div class="row text-center mt-2">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block font-weight-bold">Puntos Recompensa</small>
                                <span class="h4 font-weight-bold text-success mb-0">+ <span id="clientPuntos">1</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    let lastScannedId = null; // Para evitar spam de ajax
    
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
                if(c.descriptor_facial) {
                    try {
                        const descriptorArray = new Float32Array(Object.values(JSON.parse(c.descriptor_facial)));
                        labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(c.id.toString(), [descriptorArray]));
                    } catch (e) {
                        console.error("Error parseando descriptor de cliente " + c.id);
                    }
                }
            }

            if(labeledDescriptors.length > 0) {
                // distance threshold 0.5 (más estricto que 0.6 por defecto)
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
            }

            document.getElementById('modelLoader').className = 'badge badge-success p-2';
            document.getElementById('modelLoader').innerHTML = '<i class="fas fa-check-circle mr-1"></i> Listo (' + labeledDescriptors.length + ' rostros)';
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
        resetStatus();
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth || 640, height: video.videoHeight || 480 };
        faceapi.matchDimensions(overlay, displaySize);
        
        scanInterval = setInterval(async () => {
            if(!isScanning || !faceMatcher) return;
            
            const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            
            overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
            faceapi.draw.drawDetections(overlay, resizedDetections);
            
            const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
            
            results.forEach((result, i) => {
                const box = resizedDetections[i].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                drawBox.draw(overlay);
                
                if(result.label !== 'unknown' && result.label !== lastScannedId) {
                    procesarAsistencia(result.label);
                }
            });
        }, 1000); // 1 segundo de intervalo para no saturar el servidor
    });

    function procesarAsistencia(clienteId) {
        lastScannedId = clienteId; // Bloqueo temporal
        
        // Enviar AJAX al backend
        fetch('{{ route('asistencias.registrar') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cliente_id: clienteId })
        })
        .then(response => response.json())
        .then(data => {
            const statusBox = document.getElementById('statusBox');
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const msg = document.getElementById('statusMsg');
            const details = document.getElementById('clientDetails');
            
            statusBox.className = 'scan-status ' + data.status;
            
            if(data.status === 'success') {
                icon.className = 'fas fa-check-circle fa-4x mb-3';
                title.textContent = '¡Acceso Permitido!';
                msg.textContent = data.message;
                
                document.getElementById('clientNombre').textContent = data.cliente;
                document.getElementById('clientMembresia').textContent = 'Vence: ' + data.membresia_vence;
                document.getElementById('clientPuntos').textContent = data.puntos;
                
                if(data.foto) {
                    document.getElementById('clientFoto').src = data.foto;
                } else {
                    document.getElementById('clientFoto').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.cliente) + '&background=2563EB&color=fff';
                }
                
                details.classList.remove('d-none');
            } else if(data.status === 'warning') {
                icon.className = 'fas fa-exclamation-triangle fa-4x mb-3';
                title.textContent = 'Aviso';
                msg.textContent = data.message;
                details.classList.add('d-none');
            } else {
                icon.className = 'fas fa-times-circle fa-4x mb-3';
                title.textContent = 'Acceso Denegado';
                msg.textContent = data.message;
                details.classList.add('d-none');
            }
            
            // Limpiar status después de 5 segundos
            setTimeout(() => {
                resetStatus();
            }, 5000);
        })
        .catch(err => {
            console.error(err);
            lastScannedId = null;
        });
    }

    function resetStatus() {
        lastScannedId = null;
        const statusBox = document.getElementById('statusBox');
        statusBox.className = 'scan-status idle';
        document.getElementById('statusIcon').className = 'fas fa-user-clock fa-4x mb-3';
        document.getElementById('statusTitle').textContent = 'Esperando cliente...';
        document.getElementById('statusMsg').textContent = 'Acércate a la cámara para registrar tu asistencia.';
        document.getElementById('clientDetails').classList.add('d-none');
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', loadModelsAndData);
</script>
@endpush
EOD;
mkdir('c:/laragon/www/GymX/resources/views/asistencias', 0777, true);
file_put_contents('c:/laragon/www/GymX/resources/views/asistencias/escanear.blade.php', $content);
echo "View created.\n";