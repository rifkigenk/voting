const video = document.getElementById("video");
const statusText = document.getElementById("status");
const registerBtn = document.getElementById('registerBtn');
const nisnInput = document.getElementById('nisnInput');
const registerStatus = document.getElementById('registerStatus');

let isRegistering = false;
let modelsLoaded = false;

// Wait for face-api to load, then initialize
window.addEventListener('load', () => {
    console.log('Page loaded, checking face-api...');
    if (typeof faceapi !== 'undefined') {
        console.log('face-api loaded, starting setup...');
        setupFaceAPI();
    } else {
        console.error('face-api not loaded');
        statusText.textContent = 'Error: face-api library failed to load';
        statusText.style.color = '#ef4444';
    }
});

async function setupFaceAPI() {
    try {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.13/model/';
        console.log('Loading models from:', MODEL_URL);
        
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        
        console.log('Models loaded successfully');
        modelsLoaded = true;
        statusText.textContent = 'Camera ready';
        statusText.style.color = '#22c55e';
        startCamera();
    } catch (err) {
        console.error('Error loading models:', err);
        statusText.textContent = 'Error loading face models: ' + err.message;
        statusText.style.color = '#ef4444';
    }
}

function startCamera() {
    console.log('Starting camera...');
    navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
        .then(stream => {
            console.log('Camera stream obtained');
            video.srcObject = stream;
            video.play();
        })
        .catch(err => {
            console.error('Camera error:', err);
            statusText.textContent = 'Camera access denied: ' + err.message;
            statusText.style.color = '#ef4444';
        });
}

video.addEventListener("play", () => {
    console.log('Video playing, starting face detection');
    if (!modelsLoaded) {
        console.warn('Models not loaded yet');
        return;
    }
    setInterval(async () => {
        const detections = await faceapi
            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptors();

        if (detections.length === 1) {
            statusText.textContent = "Wajah valid";
            statusText.style.color = "#22c55e";

            const faceDescriptor = Array.from(detections[0].descriptor);

            // If in register mode, send descriptor + nisn to record_face endpoint
            if (isRegistering) {
                const nisn = nisnInput.value.trim();
                if (!nisn) {
                    registerStatus.textContent = 'Masukkan NISN sebelum mendaftarkan wajah.';
                    registerStatus.style.color = '#ef4444';
                } else {
                    registerStatus.textContent = 'Menyimpan face ID...';
                    registerStatus.style.color = '#f59e0b';
                    fetch('record_face.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ nisn: nisn, descriptor: faceDescriptor })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'ok') {
                            registerStatus.textContent = 'Registrasi wajah berhasil.';
                            registerStatus.style.color = '#22c55e';
                        } else {
                            registerStatus.textContent = 'Gagal: ' + (res.message || 'Server error');
                            registerStatus.style.color = '#ef4444';
                        }
                        // stop registering after one attempt
                        isRegistering = false;
                    })
                    .catch(err => {
                        registerStatus.textContent = 'Gagal menyimpan descriptor.';
                        registerStatus.style.color = '#ef4444';
                        isRegistering = false;
                    });
                }
            } else {
                // normal flow: check face and proceed to voting if match
                fetch("cek_wajah.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ descriptor: faceDescriptor })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "match") {
                        if (data.has_voted === 1) {
                            alert("Anda sudah melakukan voting!");
                        } else {
                            // set session via server if needed, then go to vote page
                            // Redirect to vote.php in php directory
                            window.location.href = "../../php/vote.php";
                        }
                    }
                });
            }

        } else if (detections.length > 1) {
            statusText.textContent = "Lebih dari satu wajah terdeteksi";
            statusText.style.color = "#ef4444";
        } else {
            statusText.textContent = "Arahkan wajah ke frame";
            statusText.style.color = "#facc15";
        }

    }, 1000);
});

// Register button toggles registration mode
if (registerBtn) {
    registerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        isRegistering = true;
        registerStatus.textContent = 'Arahkan wajah di frame untuk mendaftarkan...';
        registerStatus.style.color = '#f59e0b';
    });
}
