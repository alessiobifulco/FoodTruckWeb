<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'get_details' && isset($_GET['id'])) {
    if (!is_numeric($_GET['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'ID ordine non valido.']));
    }
    $order_id = intval($_GET['id']);
    $response = [];
    $stmt = $conn->prepare("SELECT * FROM Ordini WHERE id_ordine = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $main_details = $stmt->get_result()->fetch_assoc();

    if ($main_details) {
        $response['details'] = $main_details;

        $sql_products = "SELECT COALESCE(do.nome_personalizzato, p.nome) AS nome, do.quantita, do.prezzo_unitario_al_momento_ordine 
                         FROM DettagliOrdine do 
                         LEFT JOIN Prodotti p ON do.id_prodotto = p.id_prodotto 
                         WHERE do.id_ordine = ?";

        $stmt_products = $conn->prepare($sql_products);
        $stmt_products->bind_param("i", $order_id);
        $stmt_products->execute();
        $products = $stmt_products->get_result()->fetch_all(MYSQLI_ASSOC);
        $response['products'] = $products;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    header('Content-Type: application/json');
    $order_id = $_POST['order_id'] ?? null;
    $new_status = $_POST['new_status'] ?? null;
    $valid_stati = ['in_preparazione', 'in_consegna', 'consegnato', 'annullato'];
    if ($order_id && $new_status && in_array($new_status, $valid_stati)) {
        $stmt = $conn->prepare("UPDATE Ordini SET stato = ? WHERE id_ordine = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $response = ['success' => true, 'new_status_display' => str_replace('_', ' ', $new_status)];
            $next_step = null;
            switch ($new_status) {
                case 'in_preparazione':
                    $next_step = ['button_text' => 'In Consegna', 'next_status_data' => 'in_consegna', 'button_class' => 'btn-primary'];
                    break;
                case 'in_consegna':
                    $next_step = ['button_text' => 'Consegnato', 'next_status_data' => 'consegnato', 'button_class' => 'btn-primary'];
                    break;
            }
            $response['next_step'] = $next_step;
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

function time_ago($datetime)
{
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    if ($seconds <= 60) return "adesso";
    if ($minutes <= 60) return ($minutes == 1) ? "un minuto fa" : "$minutes minuti fa";
    if ($hours <= 24) return ($hours == 1) ? "un'ora fa" : "$hours ore fa";
    return ($days == 1) ? "ieri" : "$days giorni fa";
}

$ordini = [];
$sql = "SELECT o.id_ordine, o.data_ordine, o.aula_consegna, o.stato, GROUP_CONCAT(COALESCE(do.nome_personalizzato, p.nome) SEPARATOR ', ') AS prodotti
        FROM Ordini o 
        JOIN DettagliOrdine do ON o.id_ordine = do.id_ordine 
        LEFT JOIN Prodotti p ON do.id_prodotto = p.id_prodotto
        WHERE DATE(o.data_ordine) = CURDATE()
        GROUP BY o.id_ordine 
        ORDER BY FIELD(o.stato, 'ricevuto', 'in_preparazione', 'in_consegna'), o.data_ordine DESC";
$result = $conn->query($sql);
if ($result) {
    $ordini = $result->fetch_all(MYSQLI_ASSOC);
}

$page_title = "Dashboard Venditore";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Dashboard Venditore</h1>
    <section class="card">
        <h2>Ordini di Oggi</h2>
        <div id="order-list">
            <?php if (empty($ordini)): ?>
                <div class="order-notification">
                    <p>Nessun ordine per oggi al momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($ordini as $ordine): ?>
                    <div class="order-notification" id="order-<?php echo $ordine['id_ordine']; ?>">
                        <div class="detail-row header-row">
                            <div class="detail-info">
                                <span><strong>Ordine ID:</strong> #<?php echo str_pad($ordine['id_ordine'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span><strong>Orario:</strong> <?php echo time_ago($ordine['data_ordine']); ?></span>
                                <span><strong>Stato:</strong> <span class="status-badge status-<?php echo htmlspecialchars($ordine['stato']); ?>"><?php echo htmlspecialchars(str_replace('_', ' ', $ordine['stato'])); ?></span></span>
                            </div>
                            <div class="detail-actions">
                                <a href="#" class="btn btn-secondary view-details-btn" data-order-id="<?php echo $ordine['id_ordine']; ?>">Dettagli</a>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-info"><strong>Prodotti:</strong> <?php echo htmlspecialchars($ordine['prodotti']); ?></div>
                            <div class="detail-actions">
                                <?php
                                $next_status = '';
                                $button_text = '';
                                $button_class = 'btn-secondary';
                                switch ($ordine['stato']) {
                                    case 'ricevuto':
                                        $next_status = 'in_preparazione';
                                        $button_text = 'In Preparazione';
                                        break;
                                    case 'in_preparazione':
                                        $next_status = 'in_consegna';
                                        $button_text = 'In Consegna';
                                        $button_class = 'btn-primary';
                                        break;
                                    case 'in_consegna':
                                        $next_status = 'consegnato';
                                        $button_text = 'Consegnato';
                                        $button_class = 'btn-primary';
                                        break;
                                }
                                if ($next_status):
                                ?>
                                    <button class="btn <?php echo $button_class; ?> update-status-btn" data-order-id="<?php echo $ordine['id_ordine']; ?>" data-new-status="<?php echo $next_status; ?>"><?php echo $button_text; ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <section class="card">
        <h2>Gestione</h2>
        <div class="management-buttons">
            <a href="admin_menu.php" class="management-btn">Gestisci Listino Prodotti</a>
            <a href="#" class="management-btn">Gestisci Messaggi</a>
            <a href="#" class="management-btn">Gestisci Notifiche</a>
        </div>
    </section>
</div>

<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Dettagli Ordine <span id="modalOrderId"></span></h2>
        <div id="modalOrderDetails"></div>
        <h3>Prodotti Ordinati</h3>
        <ul id="modalProductList"></ul>
    </div>
</div>

<script src="js/admin.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>