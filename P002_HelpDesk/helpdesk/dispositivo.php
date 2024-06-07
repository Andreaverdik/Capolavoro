<?php
session_start();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="ricerca.css">
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

    <h2>Ricerca Dispositivi</h2>
    <div class="container mt-4">
    <h2>Cerca un dispositivo per nome</h2>
    <form id="searchForm" class="row g-3" onsubmit="event.preventDefault(); fetchDispositivo();">
        <div class="col-md-9">
            <label for="dispositivo_id" class="form-label">Nome del dispositivo</label>
            <input type="text" class="form-control" id="dispositivo_id" placeholder="Nome del dispositivo">
        </div>
        <div class="col-md-3" style="margin-top: 23px;">
            <button type="submit" class="btn btn-primary mt-4">Cerca</button>
        </div>
    </form>
    <div id="resultsContainer" class="container mt-5 row" style="display: flex;"></div>
</div>

<div class="container mt-5">
    <div id="cardContainer" class="row"></div>
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
            fetchCards(data.ruolo);
        } else {
            console.error('Role is not defined in the response');
        }
    })
    .catch(error => {
        console.error('Error fetching user role:', error);
    });
}

function fetchDispositivo() {
    const dispositivo_id = document.getElementById('dispositivo_id').value;
    const resultsContainer = document.getElementById('resultsContainer');
    const cardContainer = document.getElementById('cardContainer');

    cardContainer.innerHTML = '';
    resultsContainer.innerHTML = '<p>Loading results...</p>';

    fetch(`/helpdesk/ricerca-dispositivo?dispositivo_id=${encodeURIComponent(dispositivo_id)}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        resultsContainer.innerHTML = '';

        if (data.length === 0) {
            resultsContainer.innerHTML = '<p>No results found.</p>';
            return;
        }

        data.forEach(dispositivo => {
            resultsContainer.innerHTML += buildCardHtml(dispositivo);
        });
    })
    .catch(error => {
        console.error('Error fetching devices:', error);
        resultsContainer.innerHTML = `<p>Error: ${error.message}</p>`;
    });
}

function buildCardHtml(dispositivo) {
    let cardHtml = `
        <div class="col-md-4">
            <div class="card mb-3" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">${dispositivo.dispositivo_id}</h5>
                    <p class="card-text"><strong>Tipo:</strong> ${dispositivo.tipo}</p>
                    <div class="btn-group" role="group">
                        <a href="#" onclick="segnalazione('${dispositivo.dispositivo_id}')" class="btn btn-danger">Segnala</a>
                    </div>
                </div>
            </div>
        </div>`;
    return cardHtml;
}

function fetchCards(userRole, userId) {
    if (!userRole) {
        console.error('userRole is undefined or not provided');
        return;
    }

    let email = getCookie("userEmail");
    fetch(`/helpdesk/utente-id/${email}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch user ID');
                return response.json();
            })
            .then(data => {
                let userId = data.utente_id;

    //prendere l'id
    let query = window.location.search;
    const urlParams = new URLSearchParams(query);
    let aula_id = urlParams.get("aula_id");

    fetch(`dispositivi?aula_id=${aula_id}`)
        .then(response => response.json())
        .then(data => {
          hideMenuItemsBasedOnRole(userRole);
            const container = document.getElementById('cardContainer');
            data.forEach(card => {
            let cardHtml = `
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">${card.dispositivo_id}</h5>
                            <p class="card-text">${card.tipo}</p>
                            <a href="#" onclick="redirectToSegnalazione('${card.aula_id}', '${card.dispositivo_id}');" class="btn btn-primary" style="margin-left: 5px; background-color: red; border-color: red;">Segnala</a>`;
            if (userRole.toLowerCase() !== 'studente' && userRole.toLowerCase() !== 'amministratore') {
                cardHtml += `<a onclick='handleAlertClick("${card.aula_id}", "${userId}", "${card.dispositivo_id}")' class="btn btn-primary" style="margin-left: 5px; background-color: green; border-color: green;">Alert</a>`;
            }
            
            cardHtml += `</div></div></div>`;
            
            container.innerHTML += cardHtml;
        });
        })
        .catch(error => console.error('Error fetching data:', error));
    });
}

function handleAlertClick(aulaId, userId, dispositivoId) {
    console.log(aulaId);
    console.log(userId);
    console.log(dispositivoId);

    fetch(`http://localhost/helpdesk/create-alert?userId=${userId}&aulaId=${aulaId}&dispositivoId=${dispositivoId}`, {
        method: 'GET',  // Changed from POST to GET
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        if (data.success) {
            alert('Alert creato con successo!');
        } else {
            alert('Errore: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating alert:', error);
        alert('Errore nella creazione dell\'alert: ' + error.message);
    });
}

function redirectToSegnalazione(aulaId, dispositivoId) {
    window.location.href = `/helpdesk/segnalazioni?aula_id=${aulaId}&dispositivo_id=${dispositivoId}`;
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
