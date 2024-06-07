<?php
session_start();

$host = 'localhost';
$dbname = 'helpdesk';
$username = 'root';
$password = '';
$dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT nome FROM aula');
    $aule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query('SELECT dispositivo_id FROM dispositivo');
    $dispositivi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $selectedAulaId = $_GET['aula_id'] ?? '';
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="segnalazione.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.3/components/footers/footer-2/assets/css/footer-2.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<header>
        <div class="container" style="height: 70px;">
            <img src="img/ZuccanteSquared.png" alt="Logo Image" class="header-logo">
            <nav>
                <ul>
                    <li><a href="home">Home</a></li>
                    <li><a href="ricerche">Ricerca</a></li>
                    <li><a href="notifica">Notifiche</a></li>
                    <li id="alert-menu-item"><a href="alert">Alert</a></li>
                    <li><a href="segnalazioni">Segnalazioni</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mt-5" style="margin-bottom: 50px;">
        <h2>Form per la Segnalazione</h2>
        <form id="segnalazioneForm">
        <div class="form-group">
                <label for="titolo" name="titolo">Titolo</label>
                <input type="text" class="form-control" id="titolo" placeholder="Inserisci il titolo della segnalazione" required>
            </div>
            <div class="form-group">
                <label for="aula" name="aula">Aula</label>
                <select class="form-control" id="aula">
                    <option>Seleziona un'aula</option>
                    <?php foreach ($aule as $aula): ?>
                        <option value="<?= htmlspecialchars($aula['nome']) ?>"><?= htmlspecialchars($aula['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
    <label for="dispositivo">Dispositivo</label>
    <select class="form-control" id="dispositivo">
        <option value="" disabled selected>Seleziona un dispositivo</option>
        <?php foreach ($dispositivi as $dispositivo): ?>
            <option value="<?= htmlspecialchars($dispositivo['dispositivo_id']) ?>"><?= htmlspecialchars($dispositivo['dispositivo_id']) ?></option>
        <?php endforeach; ?>
    </select>
    <div id="selectedDevices" class="mt-2"></div>
</div>
            <div class="form-group">
                <label for="descrizione" name="descrizione">Descrizione</label>
                <textarea class="form-control" id="descrizione" rows="3" placeholder="Descrivi il problema" required></textarea>
            </div>
            <div class="form-group">
    <label for="tipo" name="tipo">Tipo</label>
    <select class="form-control" id="tipo">
        <option>Inserisci a chi è rivolta la segnalazione</option>
        <option>tecnico</option>
        <option>ata</option>
    </select>
</div>
<button type="button" class="btn btn-primary" id="submitBtn">Invia segnalazione</button>


        </form>
    </div>

    <style>
    .device-tag {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 10px;
    }
    .device-tag .remove-tag {
        cursor: pointer;
        margin-left: 8px;
        color: #f8f9fa;
    }
</style>


    <script>
      function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    fetchUserRole();
});

function fetchUserRole() {
    let email = getCookie("userEmail");
    if (!email) {
        console.error('Email not found in cookie.');
        hideMenuItemsBasedOnRole('');
        return;
    }
    
    fetch(`/helpdesk/get-ruolo/${email}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data && data.ruolo) { 
            hideMenuItemsBasedOnRole(data.ruolo);
        } else {
            console.error('Ruolo is not defined in the response');
            hideMenuItemsBasedOnRole('');
        }
    })
    .catch(error => {
        console.error('Error fetching user role:', error);
        hideMenuItemsBasedOnRole('');
    });
}

function hideMenuItemsBasedOnRole(role) {
    if (!role || role.toLowerCase() === 'studente' || role.toLowerCase() === 'amministratore') {
        var alertMenuItems = document.querySelectorAll('.alert-menu-item');
        var alertMenuItem = document.getElementById('alert-menu-item');
        alertMenuItems.forEach(function(item) {
            item.style.display = 'none';
        });
        if (alertMenuItem) {
            alertMenuItem.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dispositivoSelect = document.getElementById('dispositivo');
    const selectedDevicesContainer = document.getElementById('selectedDevices');

    dispositivoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const deviceId = selectedOption.value;
        const deviceName = selectedOption.text;

        // Prevent adding duplicate tags
        if (!document.getElementById('tag-' + deviceId) && deviceId !== "") {
            createDeviceTag(deviceId, deviceName);
        }

        // Reset the dropdown after selection
        this.selectedIndex = 0;
    });

    function createDeviceTag(deviceId, deviceName) {
        const newTag = document.createElement('span');
        newTag.id = 'tag-' + deviceId;
        newTag.classList.add('device-tag');
        newTag.textContent = deviceName;
        newTag.dataset.deviceId = deviceId; // Store deviceId in tag for submission

        const removeBtn = document.createElement('span');
        removeBtn.textContent = '✖';
        removeBtn.classList.add('remove-tag');
        removeBtn.onclick = function() {
            this.parentNode.remove();
        };

        newTag.appendChild(removeBtn);
        selectedDevicesContainer.appendChild(newTag);
    }
});

// Collects device IDs for submission
function collectDeviceIds() {
    const tags = document.querySelectorAll('.device-tag');
    const deviceIds = Array.from(tags).map(tag => tag.dataset.deviceId);
    return deviceIds;
}

function fetchUserId() {
    let email = getCookie("userEmail");
    if (!email) {
        console.error('Email not found in cookie.');
        return Promise.reject(new Error('Email not found in cookie.'));
    }
    return fetch(`/helpdesk/utente-id/${email}`)
        .then(response => {
            if (!response.ok) throw new Error(`Failed to fetch user ID with status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("Response Data:", data);
            if (data.error) {
                throw new Error(data.error);
            }
            if (typeof data.utente_id === 'undefined') {
                throw new Error("User ID not found in response.");
            }
            return data.utente_id;
        });
}

document.getElementById('submitBtn').addEventListener('click', function() {
    const form = document.getElementById('segnalazioneForm');
    const titolo = form.titolo.value.trim();
    const aulaNome = form.aula.value;
    const dispositivi = collectDeviceIds();
    const descrizione = form.descrizione.value.trim();
    const tipo = form.tipo.value;

    if (!titolo || aulaNome === "Seleziona un'aula" || !descrizione || !tipo) {
        alert('Please fill all the required fields.');
        return;
    }

    fetch(`/helpdesk/get-aula-id/${aulaNome}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                submitForm(data.aula_id, dispositivi);
            } else {
                throw new Error('Aula ID could not be fetched for the selected aula.');
            }
        })
        .catch(error => {
            console.error('Error fetching Aula ID:', error);
            alert('Error: Unable to fetch Aula ID. ' + error.message);
        });
});

function submitForm(aula_id, dispositivi) {
    fetchUserId().then(utente_id => {
        if (!utente_id) {
            alert('User ID is undefined, cannot submit form.');
            return;
        }

        const form = document.getElementById('segnalazioneForm');
        const titolo = form.titolo.value.trim();
        const descrizione = form.descrizione.value.trim();
        const tipo = form.tipo.value;

        const data = {
            utente_id: utente_id,
            titolo: titolo,
            aula_id: aula_id,
            dispositivi: dispositivi,
            descrizione: descrizione,
            tipo: tipo,
            data_creazione: new Date().toISOString(),
            data_ultima_modifica: new Date().toISOString()
        };

        fetch('segnalazione', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Success: ' + data.message);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert('Error: ' + error.message);
        });
    }).catch(error => {
        console.error('Error fetching user ID:', error);
        alert('Error: Unable to fetch user ID');
    });
}

</script>

    <!-- Footer 2 - Bootstrap Brain Component -->
<footer class="footer">

<!-- Widgets - Bootstrap Brain Component -->
<section class="bg-light py-4 py-md-5 py-xl-8 border-top border-light">
  <div class="container overflow-hidden">
    <div class="row gy-4 gy-lg-0 justify-content-xl-between">
      <div class="col-12 col-md-4 col-lg-3 col-xl-2">
        <div class="widget">
          <a href="#!">
            <img src="img/ZuccanteSquared.png" alt="BootstrapBrain Logo" width="200" height="200">
          </a>
        </div>
      </div>
      <div class="col-12 col-md-4 col-lg-3 col-xl-2">
        <div class="widget">
          <h4 class="widget-title mb-4">Informazioni</h4>
          <address class="mb-4">Indirizzo: Via Baglioni, 22</address>
          <p class="mb-1">
            <a class="link-secondary text-decoration-none" href="tel:+15057922430">Centralino: 041.5341.046</a>
          </p>
          <p class="mb-0">
            <a class="link-secondary text-decoration-none" href="mailto:demo@yourdomain.com">vetf04000t@istruzione.it</a>
          </p>
        </div>
      </div>
      <div class="col-12 col-md-4 col-lg-3 col-xl-2">
        <div class="widget">
          <h4 class="widget-title mb-4">Funzionalità</h4>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="ricerche" class="link-secondary text-decoration-none">Ricerca</a>
            </li>
            <li class="mb-2">
              <a href="notifica" class="link-secondary text-decoration-none">Notifiche</a>
            </li>
            <li class="mb-2 alert-menu-item">
  <a href="alert" class="link-secondary text-decoration-none">Alert</a>
</li>
            <li class="mb-2">
              <a href="segnalazioni" class="link-secondary text-decoration-none">Segnalazioni</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-12 col-md-4 col-lg-3 col-xl-2">
        <div class="widget">
          <h4 class="widget-title mb-4">Policy</h4>
          <ul class="list-unstyled">
          <li class="mb-2">
              <a href="#!" class="link-secondary text-decoration-none">Terms of Service</a>
            </li>
            <li class="mb-0">
              <a href="#!" class="link-secondary text-decoration-none">Privacy Policy</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

</footer>

</body>
</html>
