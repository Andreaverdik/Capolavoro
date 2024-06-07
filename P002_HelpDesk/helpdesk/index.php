<?php
$f3 = require('fatfree-core-master\base.php');
$f3->route('GET /home', function($f3){
    $view = new View();
    echo $view->render("home.php");
});

// Configurazione del Database
$db = new DB\SQL('mysql:host=localhost;dbname=helpdesk', 'root', '');

$f3->route('GET /utente-id/@email', function($f3) use ($db) {
    $email = $f3->get('PARAMS.email');
    $stmt = $db->prepare('SELECT utente_id FROM utente WHERE email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['utente_id' => $result['utente_id']]);
    } else {
        echo json_encode(['error' => 'User not found']);
        $f3->status(404);
    }
});

$f3->route('GET /get-ruolo/@email', function($f3, $params) use ($db) {
    $email = $params['email'];
    $email = $db->quote($email);

    $result = $db->exec("SELECT ruolo FROM utente WHERE email = " . $email . " LIMIT 1");

    if (count($result) > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ruolo' => $result[0]['ruolo']));
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ruolo' => 'unknown'));
    }
});

// Lettura account amministratore
$f3->route('GET /amministratore', function($f3) use ($db) {
    $result = $db->exec('SELECT * FROM utente WHERE ruolo = "amministratore"');
    echo json_encode($result);
});

// Lettura account tecnico
$f3->route('GET /tecnico', function($f3) use ($db) {
    $result = $db->exec('SELECT * FROM utente WHERE ruolo = "tecnico"');
    echo json_encode($result);
});

// Lettura account ATA
$f3->route('GET /ata', function($f3) use ($db) {
    $result = $db->exec('SELECT * FROM utente WHERE ruolo = "ata"');
    echo json_encode($result);
});

$f3->route('GET /crud', function($f3){
    $view = new View();
    echo $view->render("crud.php");
});

$f3->route('GET /utente', function($f3) use ($db) {
    $result = $db->exec('SELECT * FROM utente');
    echo json_encode($result);
});

$f3->route('GET /crud-utente', function($f3) use ($db) {
    $nome = $f3->get('GET.nome');
    $cognome = $f3->get('GET.cognome');
    $ruolo = $f3->get('GET.ruolo');

    $params = [];
    $conditions = [];

    if (!empty($nome)) {
        $conditions[] = "nome LIKE :nome";
        $params[':nome'] = '%' . $nome . '%';
    }
    if (!empty($cognome)) {
        $conditions[] = "cognome LIKE :cognome";
        $params[':cognome'] = '%' . $cognome . '%';
    }
    if (!empty($ruolo)) {
        $conditions[] = "ruolo = :ruolo";
        $params[':ruolo'] = $ruolo;
    }

    $query = "SELECT * FROM utente";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    try {
        $result = $db->exec($query, $params);
        echo json_encode($result);
    } catch (\Exception $e) {
        error_log("Database query failed: " + $e->getMessage());
        $f3->error(500, 'Internal Server Error: Database query failed.');
    }
});

$f3->route('POST /helpdesk/utente-create', function($f3) use ($db) {
    $data = json_decode($f3->get('BODY'), true);
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'No data received']);
        return;
    }
    try {
        $query = "INSERT INTO utente (email, nome, cognome) VALUES (?, ?, ?)";
        $result = $db->exec($query, [
            $data['email'], 
            $data['nome'], 
            $data['cognome']
        ]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$f3->route('POST /utente-update', function($f3) use ($db) {
    $input = json_decode($f3->get('BODY'), true);

    try {
        if (!isset($input['userId'], $input['nome'], $input['cognome'], $input['email'], $input['stato'], $input['ruolo'])) {
            throw new Exception('Missing required fields');
        }

        $result = $db->exec('UPDATE utente SET nome = ?, cognome = ?, email = ?, stato = ?, ruolo = ?, piano = ? WHERE utente_id = ?', [
            $input['nome'], $input['cognome'], $input['email'], $input['stato'], $input['ruolo'], $input['assegnazione'], $input['userId']
        ]);

        if ($result === false) {
            throw new Exception('Failed to update user');
        }

        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } catch (Exception $e) {
        $f3->status(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

$f3->route('GET /utente-delete', function($f3, $params) use ($db) {
    $id = $f3->get("GET.id");
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID non fornito']);
        $f3->status(400);
        return;
    }
    try {
        $result = $db->exec('DELETE FROM utente WHERE utente_id = ?', [$id]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Utente eliminato']);
            $f3->status(200);
        } else {
            echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
            $f3->status(404);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        $f3->status(500);
    }
});

$f3->route('GET /logout', function($f3) {
    // Distrugge tutti i cookie
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }

    // Reindirizza l'utente alla pagina di login o alla homepage
    $f3->reroute('/home');
});

// Rotta per visualizzare la pagina di ricerca
$f3->route('GET /ricerche', function($f3) {
    $view = new View();
    echo $view->render("aule.php");
});

// Ricerca delle aule
$f3->route('GET /ricerca2', function($f3) use ($db) {
    $tipo = $f3->get('GET.tipo');
    $termine = $f3->get('GET.termine');
    if ($tipo === 'dispositivo') {
         $query = 'SELECT * FROM dispositivo WHERE tipo LIKE ?';
    } else {
        $query = 'SELECT * FROM aula WHERE nome LIKE ?';
    }
    try {
        $result = $db->exec($query, ['%' . $termine . '%']);
        echo json_encode($result);
    } catch (PDOException $e) {
        $f3->error(500, 'Errore del database: ' . $e->getMessage());
    }
});

$f3->route('GET /ricerca-aule', function($f3) use ($db){

    $nome = $f3->get('GET.nome');
    $tipo = $f3->get('GET.tipo');
    $numero = $f3->get('GET.numero');
    $piano = $f3->get('GET.piano');

    $query = "SELECT * FROM aula WHERE (nome LIKE :nome OR :nome = '')
    AND (tipo LIKE :tipo OR :tipo = '')
    AND (numero LIKE :numero OR :numero = '')
    AND (piano LIKE :piano OR :piano = '')";

$result = $db->exec($query, [
':nome' => $nome ? "%$nome%" : '',
':tipo' => $tipo ? "%$tipo%" : '',
':numero' => $numero ? "%$numero%" : '',
':piano' => $piano ? "%$piano%" : ''
]);

    echo json_encode($result);
});

$f3->route('GET /dispositivo', function($f3) {
    $view = new View();
    echo $view->render("dispositivo.php");
});

// Dispositivi
$f3->route('GET /dispositivi', function($f3) use ($db) {
    $aula_id = $f3->get('GET.aula_id');
    if (!$aula_id) {
        $f3->error(404, 'Aula ID non specificato');
        return;
    }
    $query = 'SELECT * FROM dispositivo WHERE aula_id = ?';
    $result = $db->exec($query, [$aula_id]);
    echo json_encode($result);
});

$f3->route('GET /ricerca-dispositivo', function($f3, $params) use ($db) {
    $dispositivo_id = $f3->get('GET.dispositivo_id');

    $result = $db->exec("SELECT * FROM dispositivo WHERE dispositivo_id LIKE ?", ["%$dispositivo_id%"]);

    if ($result) {
        echo json_encode($result);
    } else {
        $f3->error(500, 'Internal Server Error: Query failed');
    }
});

$f3->route('GET /aula-id/@id', function($f3, $params) use ($db) {
    $aulaId = $params['id']; // Ottiene l'ID dell'aula dai parametri della route
    $stmt = $db->prepare('SELECT * FROM aula WHERE aula_id = ?'); // Prepara una query SQL per cercare l'aula
    $stmt->execute([$aulaId]); // Esegue la query con l'ID specificato
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // Recupera il risultato come array associativo

    if ($result) {
        echo json_encode($result); // Invia i dettagli dell'aula come JSON se trovata
    } else {
        echo json_encode(['error' => 'Aula not found']); // Restituisce un errore se non viene trovata alcuna aula
        $f3->status(404); // Imposta il codice di stato HTTP a 404
    }
});


// Alert
$f3->route('GET /create-alert', function($f3) use ($db) {
    $userId = $f3->get('GET.userId');
    $aulaId = $f3->get('GET.aulaId');
    $dispositivoId = $f3->get('GET.dispositivoId');

    if (is_null($userId) || is_null($aulaId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        return;
    }

    // Fetch user's floor
    $userStmt = $db->prepare("SELECT piano FROM utente WHERE utente_id = ?");
    $userStmt->execute([$userId]);
    $userPiano = $userStmt->fetchColumn();

    // Fetch aula or dispositivo's floor
    $locationPiano = null;
    if ($aulaId) {
        $aulaStmt = $db->prepare("SELECT piano FROM aula WHERE aula_id = ?");
        $aulaStmt->execute([$aulaId]);
        $locationPiano = $aulaStmt->fetchColumn();
    } else if ($dispositivoId) {
        $deviceStmt = $db->prepare("SELECT piano FROM dispositivo WHERE dispositivo_id = ?");
        $deviceStmt->execute([$dispositivoId]);
        $locationPiano = $deviceStmt->fetchColumn();
    }

    // Prevent alerts for rooms or devices on the same floor as the user
    if ($userPiano == $locationPiano) {
        echo json_encode(['success' => false, 'message' => 'Non puoi impostare un alert per aule o dispositivi sul tuo stesso piano']);
        return;
    }

    // Check if the alert already exists
    $alertSql = "SELECT 1 FROM alert WHERE utente_id = ? AND (aula_id = ? OR dispositivo_id = ?)";
    $alertStmt = $db->prepare($alertSql);
    $alertStmt->execute([$userId, $aulaId, $dispositivoId]);
    if ($alertStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Alert già esistente per questa aula/dispositivo e utente']);
        return;
    }

    // Insert new alert
    $insertSql = "INSERT INTO alert (utente_id, aula_id, dispositivo_id) VALUES (?, ?, ?)";
    $insertStmt = $db->prepare($insertSql);
    $result = $insertStmt->execute([$userId, $aulaId, $dispositivoId]);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Alert creato con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante la creazione dell\'alert']);
    }
});

$f3->route('GET /get-alerts', function($f3) use ($db) {
    try {
        // Query to fetch classroom alerts
        $stmt1 = $db->prepare("SELECT a.aula_id, a.nome AS aula_nome, a.numero AS aula_numero, a.piano AS aula_piano, a.tipo AS aula_tipo, al.alert_id FROM aula a INNER JOIN alert al ON a.aula_id = al.aula_id WHERE al.dispositivo_id IS NULL ORDER BY a.nome ASC");
        $stmt1->execute();
        $classroomAlerts = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Query to fetch device alerts
        $stmt2 = $db->prepare("SELECT d.dispositivo_id, d.tipo AS dispositivo_tipo, a.aula_id, a.nome AS aula_nome, a.numero AS aula_numero, a.piano AS aula_piano, a.tipo AS aula_tipo, al.alert_id FROM dispositivo d INNER JOIN alert al ON d.dispositivo_id = al.dispositivo_id INNER JOIN aula a ON d.aula_id = a.aula_id ORDER BY a.nome, d.dispositivo_id ASC");
        $stmt2->execute();
        $deviceAlerts = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Combine the results
        $alerts = [
            'classrooms' => $classroomAlerts,
            'devices' => $deviceAlerts
        ];

        // Set the content type to JSON and return results
        header('Content-Type: application/json');
        echo json_encode($alerts);
    } catch (PDOException $e) {
        // Handle database errors
        $f3->error(500, "Internal Server Error: " . $e->getMessage());
    }
});


$f3->route('GET /delete-alert/@alert_id', function($f3, $params) use ($db) {
    $alertId = $params['alert_id'];

    if (!is_numeric($alertId)) {
        // Ensure the alert ID is numeric
        $f3->error(400, 'Invalid alert ID');
        return;
    }

    try {
        // Prepare and execute the DELETE statement
        $stmt = $db->prepare("DELETE FROM alert WHERE alert_id = ?");
        $stmt->execute([$alertId]);

        if ($stmt->rowCount() > 0) {
            // Check if any rows were deleted
            echo json_encode(['success' => true, 'message' => 'Alert deleted successfully']);
        } else {
            // No rows deleted, maybe the ID was not found
            echo json_encode(['success' => false, 'message' => 'No alert found with that ID']);
        }
    } catch (PDOException $e) {
        // Handle SQL error or invalid inputs
        $f3->error(500, "Internal Server Error: " . $e->getMessage());
    }
});

$f3->route('GET /get-aula-id/@nome', function($f3) use ($db){
    $nome = urldecode($f3->get('PARAMS.nome'));

    try {
        $stmt = $db->prepare('SELECT aula_id FROM aula WHERE nome = ?');
        $stmt->execute([$nome]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['success' => true, 'aula_id' => $result['aula_id']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aula not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
});

$f3->route('GET /segnalazioni', function($f3) {
    $view = new View();
    echo $view->render("segnalazione.php");
});

$f3->route('POST /segnalazione', function($f3) use ($db) {
    $data = json_decode($f3->get('BODY'), true);

    if (!$data || !isset($data['utente_id'], $data['titolo'], $data['aula_id'], $data['descrizione'], $data['tipo'])) {
        echo json_encode(['success' => false, 'message' => 'Missing or invalid data']);
        return;
    }

    // Initialize variables to handle optional parameters.
    $dispositivi = $data['dispositivi'] ?? [];
    $data['dispositivo_id'] = $data['dispositivo_id'] ?? null;

    // Fetch the floor of the aula or the connected device.
    $piano = null; // Floor variable initialization
    if (!empty($data['aula_id'])) {
        $aulaStmt = $db->prepare("SELECT piano FROM aula WHERE aula_id = ?");
        $aulaStmt->execute([$data['aula_id']]);
        $piano = $aulaStmt->fetchColumn();
    } elseif (!empty($data['dispositivo_id'])) {
        $deviceStmt = $db->prepare("SELECT a.piano FROM dispositivo d JOIN aula a ON d.aula_id = a.aula_id WHERE d.dispositivo_id = ?");
        $deviceStmt->execute([$data['dispositivo_id']]);
        $piano = $deviceStmt->fetchColumn();
    }

    if ($piano === null) {
        echo json_encode(['success' => false, 'message' => 'No valid location found for provided aula or dispositivo']);
        return;
    }

    // Handle inserting reports for both aula and device.
    foreach ($dispositivi as $dispositivo_id) {
        $sql = "INSERT INTO segnalazione (utente_id, dispositivo_id, aula_id, titolo, descrizione, tipo, data_creazione, data_ultima_modifica, stato) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        try {
            $stmt->execute([
                $data['utente_id'],
                $dispositivo_id,
                $data['aula_id'],
                $data['titolo'],
                $data['descrizione'],
                $data['tipo'],
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
                'in attesa'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            return;
        }
    }

    if (empty($dispositivi)) {
        $stmt = $db->prepare("INSERT INTO segnalazione (utente_id, aula_id, titolo, descrizione, tipo, data_creazione, data_ultima_modifica, stato) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([
                $data['utente_id'],
                $data['aula_id'],
                $data['titolo'],
                $data['descrizione'],
                $data['tipo'],
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
                'in attesa'
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            return;
        }
    }
    $segnalazione_id = $db->lastInsertId();

    // Fetch and notify users who have set alerts on the specified aula or dispositivo.
    $alertUsersSql = "SELECT utente_id FROM alert WHERE aula_id = ? OR dispositivo_id = ?";
    $alertUsersStmt = $db->prepare($alertUsersSql);
    $alertUsersStmt->execute([$data['aula_id'], $data['dispositivo_id']]);
    $alertedUsers = $alertUsersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Also, fetch users on the same floor with specific roles.
    $usersOnFloorSql = "SELECT utente_id FROM utente WHERE piano = ? AND ruolo IN (?, 'ata')";
    $usersOnFloorStmt = $db->prepare($usersOnFloorSql);
    $usersOnFloorStmt->execute([$piano, $data['tipo']]);
    $usersOnFloor = $usersOnFloorStmt->fetchAll(PDO::FETCH_ASSOC);

    $allNotifiedUsers = array_unique(array_merge($alertedUsers, $usersOnFloor), SORT_REGULAR);

    if (empty($allNotifiedUsers)) {
        echo json_encode(['success' => false, 'message' => 'No corresponding users found to send notifications']);
        return;
    }

    // Create a notification and associate it with the users.
    $stmt = $db->prepare("INSERT INTO notifica (segnalazione_id) VALUES (?)");
    $stmt->execute([$segnalazione_id]);
    $notifica_id = $db->lastInsertId();

    foreach ($allNotifiedUsers as $user) {
        $stmt = $db->prepare("INSERT INTO notifiche_utente (notifica_id, utente_id) VALUES (?, ?)");
        $stmt->execute([$notifica_id, $user['utente_id']]);
    }

    echo json_encode(['success' => true, 'message' => 'Segnalazione and notification processed']);
});

$f3->route('GET /segnalazioni-aula', function($f3) {
    $aulaId = $f3->get('GET.aula_id');

    if (!empty($aulaId)) {
        // Option 1: Redirect with URL parameter
        $f3->reroute("/segnalazioni?aula_id={$aulaId}");

    } else {
        // Handle the case where aula_id is not provided or is empty
        $f3->reroute('/error-page'); // Redirect to an error page or handle error
    }
});

$f3->route('GET /notifiche', function($f3) use ($db) {

        header('Content-Type: application/json');

        try {
            $result = $db->exec("SELECT * FROM segnalazione");
    
            if ($result) {
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No segnalazioni found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
});

$f3->route('GET /notifications', function($f3, $params) use ($db) {
    $userEmail = $f3->get('COOKIE.userEmail'); // Supponiamo che l'email dell'utente sia salvata in un cookie

    // Ottenere il ruolo e l'ID dell'utente dal database utilizzando l'email
    $userStmt = $db->prepare("SELECT utente_id, ruolo, piano FROM utente WHERE email = ?");
    $userStmt->execute([$userEmail]);
    $userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userInfo) {
        echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
        return;
    }

    $userId = $userInfo['utente_id'];
    $userRole = $userInfo['ruolo'];
    $userPiano = $userInfo['piano'];

    switch ($userRole) {
case 'tecnico':
case 'ata':
    $stmt = $db->prepare("SELECT s.segnalazione_id, s.titolo, s.descrizione, s.stato, a.nome AS aula, s.dispositivo_id, s.data_creazione, s.data_ultima_modifica
                          FROM notifiche_utente nu
                          JOIN notifica n ON nu.notifica_id = n.notifica_id
                          JOIN segnalazione s ON n.segnalazione_id = s.segnalazione_id
                          JOIN aula a ON s.aula_id = a.aula_id
                          WHERE nu.utente_id = ? AND a.piano = ? AND s.tipo = ? UNION ALL
                          SELECT 
    a.alert_id AS segnalazione_id,
    CASE
        WHEN a.aula_id IS NOT NULL THEN CONCAT('Notifica Alert Aula: ', COALESCE(aula.nome, 'Non specificato'))
        WHEN a.dispositivo_id IS NOT NULL THEN CONCAT('Notifica Alert Dispositivo: ', COALESCE(d.dispositivo_id, 'Non specificato'))
        ELSE 'Alert Generico'
    END AS titolo,
    COALESCE(s.descrizione, 'Active Alert') AS descrizione,
    COALESCE(s.stato, 'Active') AS stato,
    COALESCE(aula.nome, 'Non specificato') AS aula,
    COALESCE(d.dispositivo_id, 'Non specificato') AS dispositivo,
    COALESCE(s.data_creazione, 'Non specificato') AS data_creazione,
    COALESCE(s.data_ultima_modifica, 'Non specificato') AS data_ultima_modifica
FROM alert a
LEFT JOIN dispositivo d ON a.dispositivo_id = d.dispositivo_id
LEFT JOIN aula ON a.aula_id = aula.aula_id
LEFT JOIN segnalazione s ON (s.aula_id = a.aula_id OR s.dispositivo_id = a.dispositivo_id)
WHERE a.utente_id = ?");
    $stmt->execute([$userId, $userPiano, $userRole, $userId]);
    break;

        case 'amministratore':
            // Amministratori vedono tutte le segnalazioni
            $stmt = $db->query("SELECT s.segnalazione_id, s.titolo, s.descrizione, s.stato, a.nome AS aula, s.dispositivo_id, s.data_creazione, s.data_ultima_modifica
                                FROM segnalazione s
                                JOIN aula a ON s.aula_id = a.aula_id");
            break;

        case 'studente':
            // Gli studenti vedono solo le loro segnalazioni
            $stmt = $db->prepare("SELECT s.segnalazione_id, s.titolo, s.descrizione, s.stato, a.nome AS aula, s.dispositivo_id, s.data_creazione, s.data_ultima_modifica
                                  FROM segnalazione s
                                  JOIN aula a ON s.aula_id = a.aula_id
                                  WHERE s.utente_id = ?");
            $stmt->execute([$userId]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ruolo non valido']);
            return;
    }

    $segnalazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($segnalazioni) {
        echo json_encode(['success' => true, 'segnalazioni' => $segnalazioni]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna segnalazione trovata']);
    }
});

$f3->route('POST /update-segnalazione/@id', function($f3, $params) use ($db) {
    header('Content-Type: application/json');
    $segnalazioneId = $params['id'];
    $data = json_decode($f3->get('BODY'), true);
    $newStatus = $data['stato'];
    $userEmail = $f3->get('COOKIE.userEmail'); // Assuming the user's email is stored in a cookie

    // First, verify the user's role
    $userCheckStmt = $db->prepare("SELECT ruolo FROM utente WHERE email = ?");
    $userCheckStmt->execute([$userEmail]);
    $userRole = $userCheckStmt->fetchColumn();

    // Check if the user is a technician or ATA
    if ($userRole !== 'tecnico' && $userRole !== 'ata') {
        echo json_encode(['success' => false, 'message' => 'Access not authorized: only technicians or ATA can modify the status']);
        return;
    }

    // Check if the segnalazione is related to an alert
    $stmt = $db->prepare("SELECT aula_id, dispositivo_id FROM segnalazione WHERE segnalazione_id = ?");
    $stmt->execute([$segnalazioneId]);
    $segnalazione = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($segnalazione) {
        $aulaId = $segnalazione['aula_id'];
        $dispositivoId = $segnalazione['dispositivo_id'];
    
        // Check if there is an active alert for the same aula or dispositivo
        $alertCheckStmt = $db->prepare("SELECT COUNT(*) FROM alert WHERE aula_id = ? OR dispositivo_id = ?");
        $alertCheckStmt->execute([$aulaId, $dispositivoId]);
        $isAlert = $alertCheckStmt->fetchColumn() > 0;
    
        if ($isAlert) {
            echo json_encode(['success' => false, 'message' => 'Modifications not allowed for segnalazioni related to active alerts']);
            return;
        }
    }

    // Update the status of the segnalazione if the user is authorized and it's not an alert
    $stmt = $db->prepare("UPDATE segnalazione SET stato = ?, data_ultima_modifica = NOW() WHERE segnalazione_id = ?");
    $stmt->execute([$newStatus, $segnalazioneId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully and last modification date set to current time']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status or no change detected']);
    }
});

// route per selezionare dinamicamente l'aula
$f3->route('GET /aula-segnalazione', function($f3, $params) use ($db) {
    $device_id = $params['aula_id'];
    if (empty($device_id)) {
        $f3->error(400, 'aula ID is required');
        return;
    }

    $query = 'SELECT * FROM aula WHERE aula_id = ?';
    $device = $db->exec($query, [$device_id]);

    if (!$device) {
        $f3->error(404, 'aula not found');
        return;
    }

    echo json_encode($device);
});


// Gestione delle notifiche
$f3->route('GET /notifica', function($f3) {
    $view = new View();
    echo $view->render("notifica.php");
});
$f3->route('GET /notifiche/@utente_id', function($f3, $params) use ($db) {
    $result = $db->exec('SELECT * FROM notifica WHERE utente_id=?', $params['utente_id']);
    echo json_encode($result);
});
$f3->route('POST /notifica', function($f3) use ($db) {
    $data = json_decode($f3, true);
    $query = "INSERT INTO notifica (utente_id, messaggio) VALUES (?, ?)";
    $db->exec($query, [
        $data['utente_id'], 
        $data['messaggio']
    ]);
    echo json_encode(['success' => true]);
});

// route per l'alert
$f3->route('GET /alert', function($f3){
    $view = new View();
    echo $view->render("alert.php");
});

// route per l'auth
$f3->route('GET /auth', function($f3){
    $view = new View();
    echo $view->render("auth.php");
});

// route per l'auth
$f3->route('GET /login', function($f3){
    $view = new View();
    echo $view->render("login.php");
});
$f3->run();
?>
