<?php
session_start();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Sistema</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="alert.css">
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
                    <li><a href="alert">Alert</a></li>
                    <li><a href="segnalazioni">Segnalazioni</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <h1>Alert impostati</h1>

    <div id="alertContainer" style="display: flex;" class="row"></div>

    <script>
$(document).ready(function() {
        fetchAlerts();
    });

    document.addEventListener('DOMContentLoaded', function() {
    fetchAlerts();
});

document.addEventListener('DOMContentLoaded', function() {
    fetchAlerts();
});

function fetchAlerts() {
    fetch('/helpdesk/get-alerts')
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('alertContainer');
        if (!container) {
            console.error('alertContainer not found');
            return;
        }

        container.innerHTML = '';  // Clear previous contents

        // Handle classroom alerts
        if (data.classrooms.length === 0 && data.devices.length === 0) {
            container.innerHTML = "<div class='alert alert-info' role='alert'>No active alerts found.</div>";
        } else {
            // Display classroom alerts
            data.classrooms.forEach(alert => {
                const cardHtml = `
                    <div class="col-md-4 mb-4" style="padding-left: 100px; padding-right: 100px;">
                        <div class="card h-100">
                            <div class="card-header text-white bg-primary">
                                Aula Alert per ${alert.aula_nome}
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${alert.aula_tipo}</h5>
                                <p class="card-text"><strong>Numero:</strong> ${alert.aula_numero}</p>
                                <p class="card-text"><strong>Piano:</strong> ${alert.aula_piano}</p>
                            </div>
                            <div class="card-footer">
                                <button onclick='deleteAlert(${alert.alert_id})' class="btn btn-danger">Elimina</button>
                            </div>
                        </div>
                    </div>`;
                container.innerHTML += cardHtml;
            });
            // Display device alerts
            data.devices.forEach(alert => {
                const cardHtml = `
                    <div class="col-md-4 mb-4" style="padding-left: 100px; padding-right: 100px;">
                        <div class="card h-100">
                            <div class="card-header text-white bg-success">
                                Dispositivo Alert per ${alert.aula_nome} - ${alert.dispositivo_tipo}
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${alert.dispositivo_tipo}</h5>
                                <p class="card-text"><strong>Nome:</strong> ${alert.dispositivo_id}</p>
                                <p class="card-text"><strong>Aula:</strong> ${alert.aula_nome}, Stanza ${alert.aula_numero}</p>
                                <p class="card-text"><strong>Piano:</strong> ${alert.aula_piano}</p>
                            </div>
                            <div class="card-footer">
                                <button onclick='deleteAlert(${alert.alert_id})' class="btn btn-danger">Elimina</button>
                            </div>
                        </div>
                    </div>`;
                container.innerHTML += cardHtml;
            });
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        container.innerHTML = "<div class='alert alert-danger' role='alert'>Failed to load alerts.</div>";
    });
}

    fetch('/helpdesk/get-alerts', {
        method: 'GET'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log("Received data:", data); // This will show what data looks like

        const container = document.getElementById('alertContainer');
        if (!container) {
            console.error('alertContainer not found');
            return;
        }

        container.innerHTML = ''; // Clear existing content

        // Access the 'data' array within the response object
        if (Array.isArray(data.data)) {  // Adjust this line
            if (data.data.length === 0) {
                container.innerHTML = "<div class='alert alert-info' role='alert'>No active alerts found.</div>";
            } else {
                data.data.forEach(alert => {  // Adjust this line
                    const deviceInfo = alert.device_name ? ` - ${alert.device_name}` : '';
                    const cardHtml = `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header text-white bg-success">
                                    Alert for ${alert.aula_nome}${deviceInfo}
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">${alert.aula_tipo}</h5>
                                    <p class="card-text"><strong>Room Number:</strong> ${alert.aula_numero}</p>
                                    <p class="card-text"><strong>Floor:</strong> ${alert.aula_piano}</p>
                                    ${alert.device_name ? `<p class="card-text"><strong>Device:</strong> ${alert.device_name}</p>` : ''}
                                </div>
                                <div class="card-footer">
                                    <button onclick='deleteAlert(${alert.alert_id})' class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>`;
                    container.innerHTML += cardHtml;
                });
            }
        } else {
            console.error('Expected data.data to be an array but got:', typeof data.data);
            container.innerHTML = "<div class='alert alert-danger' role='alert'>Error: Data field is not an array.</div>";
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        container.innerHTML = "<div class='alert alert-danger' role='alert'>Failed to load alerts.</div>";
    });

    function deleteAlert(alertId) {
    if (confirm('Are you sure you want to delete this alert?')) {
        fetch(`/helpdesk/delete-alert/${alertId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Alert deleted successfully');
                    // Optionally refresh the page or update the UI accordingly
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error deleting alert:', error));
    }
    return false;
}

</script>

<style>
    
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
            <li class="mb-2">
              <a href="ricerche" class="link-secondary text-decoration-none">Ricerca</a>
            </li>
            <li class="mb-2">
              <a href="notifica" class="link-secondary text-decoration-none">Notifiche</a>
            </li>
            <li class="mb-2">
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
