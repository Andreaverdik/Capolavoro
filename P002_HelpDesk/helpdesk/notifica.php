<?php
session_start();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="notifica.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.3/components/footers/footer-2/assets/css/footer-2.css">
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

      <!-- Form per la gestione delle notifiche -->
    <!--<h2>Notifiche</h2>
    <div class="container mt-4">
    <div id="segnalazioneContainer" class="row">

    </div>
</div>-->

<h2>Notifiche</h2>
<div class="container mt-5">
<div id="notificationsContainer"  class="row"></div>
</div>

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
    fetchNotifications();
});

function fetchNotifications(role) {
    const container = document.getElementById('notificationsContainer');
    container.innerHTML = '<p>Caricamento delle notifiche in corso...</p>';

    fetch('/helpdesk/notifications')
    .then(response => response.json())
    .then(data => {
        console.log(data); // Check what is actually received from the server
        container.innerHTML = '';
        if (!data.success) {
            console.error('Failed to fetch notifications:', data.message);
            container.innerHTML = `<p>Error: ${data.message}</p>`;
            return;
        }

        // Use the correct key 'segnalazioni' to retrieve the notifications array
        const notifications = data.segnalazioni;
        if (!Array.isArray(notifications)) {
            console.error('Expected an array but got:', typeof notifications);
            container.innerHTML = '<p>The data received is not an array.</p>';
            return;
        }

        notifications.forEach((notifica, index) => {
            let dropdownHTML = `<span>${notifica.stato}</span>`; // Default to static display

            if (!role || role.toLowerCase() === 'tecnico' || role.toLowerCase() === 'ata') {
                dropdownHTML = `
                <select onchange="updateStatus(${notifica.segnalazione_id}, this.value)">
                    <option value="in attesa" ${notifica.stato === 'in attesa' ? 'selected' : ''}>In attesa</option>
                    <option value="completata" ${notifica.stato === 'completata' ? 'selected' : ''}>Completata</option>
                    <option value="fallita" ${notifica.stato === 'fallita' ? 'selected' : ''}>Fallita</option>
                </select>`;
            }

            container.innerHTML += `
                <div class="col-md-4" style="margin-left: 150px;">
                    <div class="card mb-3">
                        <div class="card-body">
                            <span class="status-indicator" style="height: 25px; width: 25px; background-color: ${getStatusColor(notifica.stato)}; border-radius: 50%; display: inline-block; margin-left: 94%;"></span>
                            <h5 class="card-title">${notifica.titolo}</h5>
                            <p class="card-text">Descrizione: ${notifica.descrizione}</p>
                            <p class="card-text">Aula: ${notifica.aula}</p>
                            <p class="card-text">Dispositivo: ${notifica.dispositivo_id || 'N/A'}</p>
                            <p class="card-text">Stato: ${dropdownHTML}</p>
                            <p class="card-text">Data Creazione: ${notifica.data_creazione}</p>
                            <p class="card-text">Data Ultima Modifica: ${notifica.data_ultima_modifica}</p>
                        </div>
                    </div>
                </div>`;

            if (index % 2 === 1) {
                container.innerHTML += '<div class="w-100"></div>'; // Adds a break every two cards
            }
        });
    })
    .catch(error => {
        console.error('Failed to fetch notifications:', error);
        container.innerHTML = `<p>Error fetching notifications: ${error.message}</p>`;
    });
}



function getStatusColor(status) {
    switch (status) {
        case 'in attesa':
            return 'yellow';
        case 'completata':
            return 'green';
        case 'fallita':
            return 'red';
        default:
            return 'grey';
    }
}


function updateStatus(segnalazioneId, newStatus) {
    fetch(`update-segnalazione/${segnalazioneId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ stato: newStatus, data_ultima_modifica: new Date().toISOString() })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Stato aggiornato con successo');
            location.reload(); // Ricarica la pagina per aggiornare le informazioni visualizzate
        } else {
            alert('Errore nell\'aggiornamento dello stato: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        alert('Errore durante l\'aggiornamento dello stato: ' + error.message);
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
