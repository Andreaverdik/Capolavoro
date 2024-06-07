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
                    <li id="alert-menu-item"><a href="alert">Alert</a></li>
                    <li id="segnalazione-menu-item"><a href="segnalazioni">Segnalazioni</a></li>
                </ul>
            </nav>
        </div>
    </header>
    

    <h1 style="margin-top: 15px; margin-left: 572px;">Benvenuto a HelpDesk!</h1>

    <div id="container"></div>

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
    adjustMenuForLoginStatus();
});

function adjustMenuForLoginStatus() {
    let email = getCookie("userEmail");
    if (!email) {
        hideAllMenuItemsExceptHome();
        hideFooterFunctionalities();
    } else {
        fetchUserRole();
    }
}

function hideAllMenuItemsExceptHome() {
    document.querySelectorAll('nav ul li').forEach(function(item) {
        if (!item.querySelector('a[href="home"]')) {
            item.style.display = 'none';
        }
    });
}

function hideFooterFunctionalities() {
    document.querySelectorAll('.footer .widget').forEach(function(widget, index) {
        if (index === 2) {
            widget.style.display = 'none';
        }
        if (index === 1) {
            widget.style.marginLeft = '95%';
        }
    });
}

function fetchUserRole() {
  let email = getCookie("userEmail");
  if (email) {
    fetch(`/helpdesk/get-ruolo/${email}`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`Network response was not ok: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        const container = document.getElementById('container');
        console.log('User role:', data.ruolo);
        renderCardsByRole(data.ruolo, container);
        const userRole = data.ruolo;
        hideMenuItemsBasedOnRole(userRole);
        hideMenuItemsBasedOnRole2(userRole);
      })
      .catch(error => {
        console.error('Error fetching data:', error);
        const container = document.getElementById('container');
        container.innerHTML = `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-top: 30px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Login</h5>
                  <p class="card-text">Esegui il login.</p>
                  <a href="auth" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/person.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>`;
      });
  } else {
    console.error('Email not found in cookie.');
    document.getElementById('container').innerHTML = `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-top: 30px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Login</h5>
                  <p class="card-text">Esegui il login.</p>
                  <a href="auth" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/person.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>`;
  }
}

document.addEventListener('DOMContentLoaded', function() {
    var logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function() {
            fetch('helpdesk/logout', { method: 'GET' })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    console.error('Logout failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    } else {
        console.log('Logout button not found');
    }
});

function renderCardsByRole(role, container) {
  if (!role) {
    console.error('No role provided.');
    container.innerHTML = `<p>Ruolo non fornito.</p>`;
    return;
  }
  switch (role.toLowerCase()) {
    case 'studente':
      container.innerHTML = generateStudenteCards();
      break;
    case 'tecnico':
    case 'ata':
      container.innerHTML = generateTechAtaCards();
      break;
    case 'amministratore':
      container.innerHTML = generateAdminCards();
      break;
    case 'unknown':
      container.innerHTML = generateUnknownCards();
    default:
      console.error(`Unhandled role: ${role}`);
      container.innerHTML = generateDefaultCards();
      break;
  }
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

function hideMenuItemsBasedOnRole2(role) {
    if (!role || role.toLowerCase() === 'unknown') {
      var alertMenuItems = document.querySelectorAll('.ricerche-menu-item');
      var alertMenuItems = document.querySelectorAll('.notifica-menu-item');
        var alertMenuItems = document.querySelectorAll('.alert-menu-item');
        var alertMenuItems = document.querySelectorAll('.segnalazione-menu-item');
        var alertMenuItem = document.getElementById('ricerche-menu-item');
        var alertMenuItem = document.getElementById('notifica-menu-item');
        var alertMenuItem = document.getElementById('alert-menu-item');
        var alertMenuItem = document.getElementById('segnalazione-menu-item');
        alertMenuItems.forEach(function(item) {
            item.style.display = 'none';
        });
        if (alertMenuItem) {
            alertMenuItem.style.display = 'none';
        }
    }
}

function generateStudenteCards() {
    return `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Logout</h5>
                  <p class="card-text">Effettua il logout.</p>
                  <a href="/helpdesk/logout" class="btn btn-primary" id="logoutButton">Logout</a>
              </div>
              <img src="img/door-closed.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
    
    <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Ricerca</h5>
                  <p class="card-text">Cerca aule e dispositivi.</p>
                  <a href="ricerche" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/search.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
    <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title">Notifiche</h5>
                <p class="card-text">Visualizza le ultime notifiche.</p>
                <a href="notifica" class="btn btn-primary">Visita</a>
            </div>
            <img src="img/bell.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-bottom: 30px;">
  <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
          <div>
              <h5 class="card-title">Segnalazioni</h5>
              <p class="card-text">Invia e gestisci segnalazioni.</p>
              <a href="segnalazioni" class="btn btn-primary">Visita</a>
          </div>
          <img src="img/flag.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
      </div>
  </div>
</div>`;
}

function generateTechAtaCards() {
    return `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Logout</h5>
                  <p class="card-text">Effettua il logout.</p>
                  <a href="/helpdesk/logout" class="btn btn-primary" id="logoutButton">Logout</a>
              </div>
              <img src="img/door-closed.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
    
    <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Ricerca</h5>
                  <p class="card-text">Cerca aule e dispositivi.</p>
                  <a href="ricerche" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/search.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
    <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title">Notifiche</h5>
                <p class="card-text">Visualizza le ultime notifiche.</p>
                <a href="notifica" class="btn btn-primary">Visita</a>
            </div>
            <img src="img/bell.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
  <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
          <div>
              <h5 class="card-title">Alert</h5>
              <p class="card-text">Imposta e gestisci alert.</p>
              <a href="alert" class="btn btn-primary">Visita</a>
          </div>
          <img src="img/exclamation-octagon.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
      </div>
  </div>
</div>

<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-bottom: 30px;">
  <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
          <div>
              <h5 class="card-title">Segnalazioni</h5>
              <p class="card-text">Invia e gestisci segnalazioni.</p>
              <a href="segnalazioni" class="btn btn-primary">Visita</a>
          </div>
          <img src="img/flag.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
      </div>
  </div>
</div>`;
}

function generateAdminCards() {
    return `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Logout</h5>
                  <p class="card-text">Effettua il logout.</p>
                  <a href="/helpdesk/logout" class="btn btn-primary" id="logoutButton">Logout</a>
              </div>
              <img src="img/door-closed.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>

  <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Crud</h5>
                  <p class="card-text">Gestisci gli account istituzionali.</p>
                  <a href="/helpdesk/crud" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/person-gear.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
    
    <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Ricerca</h5>
                  <p class="card-text">Cerca aule e dispositivi.</p>
                  <a href="ricerche" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/search.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>
  
  <div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px;">
    <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title">Notifiche</h5>
                <p class="card-text">Visualizza le ultime notifiche.</p>
                <a href="notifica" class="btn btn-primary">Visita</a>
            </div>
            <img src="img/bell.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
        </div>
    </div>
</div>

<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-bottom: 30px;">
  <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
          <div>
              <h5 class="card-title">Segnalazioni</h5>
              <p class="card-text">Invia e gestisci segnalazioni.</p>
              <a href="segnalazioni" class="btn btn-primary">Visita</a>
          </div>
          <img src="img/flag.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
      </div>
  </div>
</div>`;
}

function generateUnknownCards() {
    return `<h1>Account non valido</h1>`
}

function generateDefaultCards() {
    return `<div class="col-md-6 col-lg-3 mb-4" style="margin-left: 575px; margin-top: 30px;">
      <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                  <h5 class="card-title">Login</h5>
                  <p class="card-text">Esegui il login.</p>
                  <a href="auth" class="btn btn-primary">Visita</a>
              </div>
              <img src="img/person.svg" alt="Search Icon" style="width: 70px; height: 80px; margin-right: 40px;">
          </div>
      </div>
  </div>`;
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
              <li class="mb-2 ricerche-menu-item">
                <a href="ricerche" class="link-secondary text-decoration-none">Ricerca</a>
              </li>
              <li class="mb-2 notifica-menu-item">
                <a href="notifica" class="link-secondary text-decoration-none">Notifiche</a>
              </li>
              <li class="mb-2 alert-menu-item">
  <a href="alert" class="link-secondary text-decoration-none">Alert</a>
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
