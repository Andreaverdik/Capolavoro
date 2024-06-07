<?php
session_start();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="home.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.3/components/footers/footer-2/assets/css/footer-2.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

    <header>
        <div class="container" style="height: 70px;">
            <img src="img/ZuccanteSquared.png" alt="Logo Image" class="header-logo">
            <nav>
                <ul>
                    <li><a href="home">Home</a></li>
                    <li id="ricerche-menu-item"><a href="ricerche">Ricerca</a></li>
                    <li id="notifica-menu-item"><a href="notifica">Notifiche</a></li>
                    <li id="segnalazione-menu-item"><a href="segnalazioni">Segnalazioni</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
    <h1 style="margin-top: 10px; margin-bottom: 70px; text-align: center;">Gestione Operazioni CRUD</h1>

    <div id="crudForm" class="mb-3" style="margin-left: 250px; margin-right: 250px;">
    <h3>Aggiunta di un nuovo account</h3>
        <input type="hidden" id="userId" value="">
        <input type="text" id="nome" placeholder="Nome" class="form-control mb-2">
        <input type="text" id="cognome" placeholder="Cognome" class="form-control mb-2">
        <input type="email" id="email" placeholder="Email" class="form-control mb-2">
        <button onclick="saveUser()" class="btn btn-primary">Salva</button>
    </div>
    </div>

    <div class="container mt-3" style="padding-top: 50px; margin-bottom: 70px; padding-left: 145px;">
    <h2>Cerca Utente</h2>
    <form id="searchForm">
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchNome" placeholder="Nome" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchCognome" placeholder="Cognome" required>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="searchRuolo">
                    <option value="">Seleziona Ruolo</option>
                    <option value="studente">Studente</option>
                    <option value="tecnico">Tecnico</option>
                    <option value="amministratore">Amministratore</option>
                    <option value="ata">ATA</option>
                </select>
            </div>
            <div class="col-md-3">
            <button type="button" class="btn btn-primary" onclick="searchUsers()">Cerca</button>
            </div>
        </div>
    </form>
</div>

<div class="container">
    <table class="table table-striped" style="margin-top: 100px;">
    <h3>Utenti della scuola</h3>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Email</th>
                <th>Ruolo</th>
                <th>Stato</th>
                <th>Operazioni</th>
            </tr>
        </thead>
        <tbody id="userTable">

        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
    fetchUsers();

    $('#crudForm').on('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
});

function fetchUsers() {
    fetch('/helpdesk/utente', {
        method: 'GET'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data && Array.isArray(data)) {
            let rows = '';
            data.forEach(user => {
                let assegnazioneCell = '';
                if (user.ruolo !== 'amministratore' && user.ruolo !== 'studente') {
                    assegnazioneCell = `
                        <td>
                            <select id="assegnazione_${user.utente_id}" class="form-control table-form-control select-transparent">
                                <option value="piano terra" ${user.piano === 'piano terra' ? 'selected' : ''}>Piano Terra</option>
                                <option value="primo piano" ${user.piano === 'primo piano' ? 'selected' : ''}>Primo Piano</option>
                                <option value="secondo piano" ${user.piano === 'secondo piano' ? 'selected' : ''}>Secondo Piano</option>
                            </select>
                        </td>`;
                }

                rows += `
                    <tr>
                        <td><input type="text" id="nome_${user.utente_id}" value="${user.nome}" class="form-control table-form-control"></td>
                        <td><input type="text" id="cognome_${user.utente_id}" value="${user.cognome}" class="form-control table-form-control"></td>
                        <td><input type="email" id="email_${user.utente_id}" value="${user.email}" class="form-control table-form-control" style="padding-right: 100px;"></td>
                        <td>
                            <select id="stato_${user.utente_id}" class="form-control table-form-control select-transparent">
                                <option value="attivo" ${user.stato === 'attivo' ? 'selected' : ''}>Attivo</option>
                                <option value="bannato" ${user.stato === 'bannato' ? 'selected' : ''}>Bannato</option>
                            </select>
                        </td>
                        <td>
                            <select id="ruolo_${user.utente_id}" class="form-control table-form-control select-transparent">
                                <option value="studente" ${user.ruolo === 'studente' ? 'selected' : ''}>Studente</option>
                                <option value="tecnico" ${user.ruolo === 'tecnico' ? 'selected' : ''}>Tecnico</option>
                                <option value="amministratore" ${user.ruolo === 'amministratore' ? 'selected' : ''}>Amministratore</option>
                                <option value="ata" ${user.ruolo === 'ata' ? 'selected' : ''}>ATA</option>
                            </select>
                        </td>
                        ${assegnazioneCell}
                        <td style="display: flex;">
                            <button onclick="updateUser(${user.utente_id})" class="btn btn-warning" style="margin-left: 20px;">Modifica</button>
                            <button onclick="deleteUser(${user.utente_id})" class="btn btn-danger" style="margin-left: 7px;">Elimina</button>
                        </td>
                    </tr>`;
            });
            document.getElementById('userTable').innerHTML = rows;
        }
    })
    .catch(error => {
        console.error('Error fetching the users:', error);
        alert('Errore durante l ottenimento dei dati: ' + error.message);
    });
}

$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        searchUsers();
    });
});

function searchUsers() {
    let nome = document.getElementById('searchNome').value;
    let cognome = document.getElementById('searchCognome').value;
    let ruolo = document.getElementById('searchRuolo').value;

    let url = `/helpdesk/crud-utente?nome=${encodeURIComponent(nome)}&cognome=${encodeURIComponent(cognome)}&ruolo=${encodeURIComponent(ruolo)}`;

    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(users => {
        if (users.length > 0) {
            updateTable(users);
        } else {
            document.getElementById('userTable').innerHTML = '<tr><td colspan="5">Nessun utente trovato.</td></tr>';
        }
    })
    .catch(error => {
        console.error("Errore nella ricerca: " + error);
        document.getElementById('userTable').innerHTML = '<tr><td colspan="5">Errore durante il caricamento degli utenti. Dettagli: ' + error.message + '</td></tr>';
    });
}

function updateTable(users) {
    let html = '';
    users.forEach(function(user) {
        let assegnazioneCell = '';
        if (user.ruolo !== 'amministratore' && user.ruolo !== 'studente') {
            assegnazioneCell = `
                <td>
                    <select id="assegnazione_${user.utente_id}" class="form-control table-form-control select-transparent">
                        <option value="piano terra" ${user.piano === 'piano terra' ? 'selected' : ''}>Piano Terra</option>
                        <option value="primo piano" ${user.piano === 'primo piano' ? 'selected' : ''}>Primo Piano</option>
                        <option value="secondo piano" ${user.piano === 'secondo piano' ? 'selected' : ''}>Secondo Piano</option>
                    </select>
                </td>`;
        }

        html += `
            <tr>
                <td><input type="text" id="nome_${user.utente_id}" value="${user.nome}" class="form-control table-form-control"></td>
                <td><input type="text" id="cognome_${user.utente_id}" value="${user.cognome}" class="form-control table-form-control"></td>
                <td><input type="email" id="email_${user.utente_id}" value="${user.email}" class="form-control table-form-control" style="padding-right: 100px;"></td>
                <td>
                    <select id="ruolo_${user.utente_id}" class="form-control table-form-control select-transparent">
                        <option value="studente" ${user.ruolo === 'studente' ? 'selected' : ''}>Studente</option>
                        <option value="tecnico" ${user.ruolo === 'tecnico' ? 'selected' : ''}>Tecnico</option>
                        <option value="amministratore" ${user.ruolo === 'amministratore' ? 'selected' : ''}>Amministratore</option>
                        <option value="ata" ${user.ruolo === 'ata' ? 'selected' : ''}>ATA</option>
                    </select>
                </td>
                <td>
                    <select id="stato_${user.utente_id}" class="form-control table-form-control select-transparent">
                        <option value="attivo" ${user.stato === 'attivo' ? 'selected' : ''}>Attivo</option>
                        <option value="bannato" ${user.stato === 'bannato' ? 'selected' : ''}>Bannato</option>
                    </select>
                </td>
                ${assegnazioneCell}
                <td style="display: flex;">
                            <button onclick="updateUser(${user.utente_id})" class="btn btn-warning" style="margin-left: 20px;">Modifica</button>
                            <button onclick="deleteUser(${user.utente_id})" class="btn btn-danger" style="margin-left: 7px;">Elimina</button>
                </td>
            </tr>`;
    });
    $('#userTable').html(html);
}

function saveUser() {
    let nome = document.getElementById('nome').value;
    let cognome = document.getElementById('cognome').value;
    let email = document.getElementById('email').value;

    let data = { nome, cognome, email };

    fetch('helpdesk/utente-create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            alert('User added successfully!');
            fetchUsers();
            document.querySelectorAll('#crudForm input').forEach(input => input.value = ''); // Clear the form fields
        } else {
            alert('Error: ' + result.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deleteUser(id) {
    console.log(`Attempting to delete user with ID: ${id}`);

    if (!id) {
        alert('ID utente non valido.');
        return;
    }

    if (confirm('Sei sicuro di voler eliminare questo utente?')) {
        fetch(`utente-delete?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                alert('Utente eliminato con successo.');
                fetchUsers();
            } else {
                throw new Error(result.message || 'Failed to delete the user.');
            }
        })
        .catch(error => {
            alert(`Errore durante l'eliminazione: ${error.message}`);
        });
    }
}

function updateUser(userId) {
    const nome = document.getElementById(`nome_${userId}`).value;
    const cognome = document.getElementById(`cognome_${userId}`).value;
    const email = document.getElementById(`email_${userId}`).value;
    const stato = document.getElementById(`stato_${userId}`).value;
    const ruolo = document.getElementById(`ruolo_${userId}`).value;
    const assegnazione = document.getElementById(`assegnazione_${userId}`) ? document.getElementById(`assegnazione_${userId}`).value : null;

    const userData = {
        userId,
        nome,
        cognome,
        email,
        stato,
        ruolo,
        assegnazione
    };

    fetch(`utente-update?id=${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User updated successfully!');
        } else {
            alert(`Failed to update user: ${data.error}`);
        }
    })
    .catch(error => {
        console.error('Error updating user:', error);
        alert('Error updating user: ' + error.message);
    });
}

</script>

<style>
.table-form-control {
    border: none;
    outline: none;
    box-shadow: none;
    background: transparent;
    color: inherit;
    font-size: inherit;
}

.table-form-control.select-transparent {
    appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"></path></svg>');
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 12px;
}

.table-form-control:focus {
    border-color: inherit;
}

.table-form-control {
    padding: 0px 8px;
}

</style>

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
            <li class="mb-2 ricerche-menu-item">
              <a href="ricerche" class="link-secondary text-decoration-none">Ricerca</a>
            </li>
            <li class="mb-2 notifica-menu-item">
              <a href="notifica" class="link-secondary text-decoration-none">Notifiche</a>
            </li>
            <li class="mb-2 segnalazione-menu-item">
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
