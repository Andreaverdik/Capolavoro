<?php
session_start();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema </title>
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

    <h2>Aule disponibili</h2>
    <div class="container mt-4">
    <h2>Ricerca aule</h2>
    <form id="searchForm" class="row g-3" onsubmit="event.preventDefault(); fetchAule();">
        <div class="col-md-3">
            <label for="nome" class="form-label">Nome dell'aula</label>
            <input type="text" class="form-control" id="nome" placeholder="Nome dell'aula">
        </div>
        <div class="col-md-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-control" id="tipo">
                <option value="">Seleziona il tipo</option>
                <option value="classe">Classe</option>
                <option value="laboratorio">Laboratorio</option>
                <option value="wc">WC</option>
                <option value="palestra">Palestra</option>
                <option value="spogliatoio">Spogliatoio</option>
                <option value="ufficio">Ufficio</option>
                <option value="assistente tecnico">Assistente Tecnico</option>
                <option value="bar">Bar</option>
                <option value="aula">Aula</option>
                <option value="sala server">Sala Server</option>
                <option value="magazzino">Magazzino</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="numero" class="form-label">Numero</label>
            <input type="text" class="form-control" id="numero" placeholder="Numero">
        </div>
        <div class="col-md-3">
            <label for="piano" class="form-label">Piano</label>
            <select class="form-control" id="piano">
                <option value="">Seleziona il piano</option>
                <option value="piano terra">Piano Terra</option>
                <option value="primo piano">Primo Piano</option>
                <option value="secondo piano">Secondo Piano</option>
            </select>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-primary" onclick="fetchAule()">Cerca</button>
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

        fetch("/helpdesk/utente-id/"+ email).then((v)=>v.json()).then((json)=>{
            if (data && data.ruolo) {
            fetchCards(data.ruolo, json["utente_id"]);
        } else {
            console.error('Role is not defined in the response');
        }
        });

    })

}

function fetchAule() {
    let userId = 1;

    let userEmail = getCookie("userEmail");  // Assumi che il cookie userEmail sia impostato
            if (!userEmail) {
                console.error('Email not found in cookie.');
                return;
            }

            fetch(`/helpdesk/utente-id/${userEmail}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch user ID');
                return response.json();
            })
            .then(data => {
                let userId = data.utente_id;

                const nome = document.getElementById('nome').value;
    const tipo = document.getElementById('tipo').value;
    const numero = document.getElementById('numero').value;
    const piano = document.getElementById('piano').value;

    const resultsContainer = document.getElementById('resultsContainer');
    const cardContainer = document.getElementById('cardContainer');

    cardContainer.innerHTML = '';
    resultsContainer.innerHTML = '<p>Loading results...</p>'; 

    fetch(`/helpdesk/ricerca-aule?nome=${encodeURIComponent(nome)}&tipo=${encodeURIComponent(tipo)}&numero=${encodeURIComponent(numero)}&piano=${encodeURIComponent(piano)}`)
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
        fetch(`/helpdesk/get-ruolo/${userEmail}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(datiUtente => {
        // console.log(datiUtente);console.log();
        

        data.forEach(aula => {
            resultsContainer.innerHTML += buildCardHtml(aula, datiUtente["ruolo"], userId);
        });
    });
    })
                  // Chiama la funzione per caricare le aule basate sull'ID utente
            });
    }

function buildCardHtml(aula, userRole, userId) {
    let cardHtml = `
        <div class="col-md-4">
            <div class="card mb-3" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">${aula.nome}</h5>
                    <p class="card-text"><strong>Tipo:</strong> ${aula.tipo}</p>
                    <p class="card-text"><strong>Numero:</strong> ${aula.numero || 'N/A'}</p>
                    <p class="card-text"><strong>Piano:</strong> ${aula.piano}</p>
                    <div class="btn-group" role="group" aria-label="Basic example">`;

    const excludedTypes = ['wc', 'magazzino', 'palestra', 'spogliatoio', 'bar'];
    if (aula.tipo && !excludedTypes.includes(aula.tipo.toLowerCase())) {
        cardHtml += `<a href="dispositivo?aula_id=${aula.aula_id}" class="btn btn-primary">Dispositivi</a>`;
    }
    cardHtml += `<a href="#" onclick="segnalazione('${aula.aula_id}')" class="btn btn-danger" style="margin-left: 5px;">Segnala</a>`;

    if ((userRole === 'tecnico' || userRole === 'ata' )) {
        cardHtml += `<a onclick='handleAlertClick(${aula.aula_id}, ${userId})' class="btn btn-success" style="margin-left: 5px;">Alert</a>`;
    }

    cardHtml += `</div></div></div></div>`;
    return cardHtml;
}

function fetchCards(userRole, userId) {
    fetch('/helpdesk/ricerca2')
    .then(response => response.json())
    .then(data => {
        hideMenuItemsBasedOnRole(userRole);
        const container = document.getElementById('cardContainer');
        
        if (!container) {
            console.error('Container element not found');
            return;
        }
        
        container.innerHTML = '';
        
        if (data.length === 0) {
            container.innerHTML = '<p>No results found.</p>';
            return;
        }

        data.forEach(card => {
            let cardHtml = `
                <div class="col-md-4">
                    <div class="card mb-3" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">${card.nome}</h5>
                            <p class="card-text"><strong>Tipo:</strong> ${card.tipo}</p>
                            <p class="card-text"><strong>Numero:</strong> ${card.numero || 'N/A'}</p>
                            <p class="card-text"><strong>Piano:</strong> ${card.piano}</p>
                            <div class="btn-group" role="group" aria-label="Basic example">`;

            const excludedTypes = ['wc', 'magazzino', 'palestra', 'spogliatoio', 'bar'];
            if (!excludedTypes.includes(card.tipo.toLowerCase())) {
                cardHtml += `<a href="dispositivo?aula_id=${card.aula_id}" class="btn btn-primary">Dispositivi</a>`;
            }

            cardHtml += `<a href="#" onclick="segnalazione('${card.aula_id}')" class="btn btn-danger" style="margin-left: 5px;">Segnala</a>`;

            if (userRole === 'tecnico' || userRole === 'ata' ) {
        cardHtml += `<a onclick='handleAlertClick(${card.aula_id}, ${userId})' class="btn btn-success" style="margin-left: 5px;">Alert</a>`;
    }
            cardHtml += `</div></div></div></div>`;
            container.innerHTML += cardHtml;
        });
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        alert('Error: Unable to fetch data');
    });
}

function handleAlertClick(aulaId, userId) {
    console.log(aulaId);
    console.log(userId);

    // Building the URL with query parameters

    fetch(`http://localhost/helpdesk/create-alert?userId=${userId}&aulaId=${aulaId}`, {
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
            
    }})
    .catch(error => {
        console.error('Error creating alert:', error);
        alert('Errore nella creazione dell\'alert: ' + error.message);
    });
}

function segnalazione(aulaId) {
    window.location.href = `/helpdesk/segnalazioni-aula?aula_id=${aulaId}`;
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
