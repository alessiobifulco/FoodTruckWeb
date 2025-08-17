<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_product') {
        $stmt = $conn->prepare("INSERT INTO Prodotti (nome, descrizione, prezzo, categoria) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_POST['categoria']);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit_product') {
        $stmt = $conn->prepare("UPDATE Prodotti SET nome=?, descrizione=?, prezzo=?, categoria=? WHERE id_prodotto=?");
        $stmt->bind_param("ssdsi", $_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_POST['categoria'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: admin_menu.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Prodotti WHERE id_prodotto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_menu.php");
    exit();
}

$result = $conn->query("SELECT * FROM Prodotti ORDER BY categoria, nome");
$prodotti = $result->fetch_all(MYSQLI_ASSOC);

$page_title = "Gestione Prodotti";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Gestione Prodotti</h1>
    <a href="admin.php" class="btn-back-dashboard">⬅ Torna alla Dashboard</a>

    <section class="card">
        <h2>Aggiungi Nuovo Prodotto</h2>
        <form method="POST" action="admin_menu.php">
            <input type="hidden" name="action" value="add_product">
            <div class="form-row">
                <div class="form-group flex-3">
                    <label>Nome:</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group flex-1">
                    <label>Prezzo (€):</label>
                    <input type="number" step="0.01" name="prezzo" required>
                </div>
                <div class="form-group flex-2">
                    <label>Categoria:</label>
                    <select name="categoria" required>
                        <option value="panino_predefinito">Panino predefinito</option>
                        <option value="pizzetta">Pizzetta</option>
                        <option value="bevanda">Bevanda</option>
                        <option value="panino_componibile">Panino componibile (base)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Descrizione:</label>
                <textarea name="descrizione" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-add">Aggiungi Prodotto</button>
        </form>
    </section>

    <section class="card">
        <h2>Elenco Prodotti</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Prezzo</th>
                <th>Categoria</th>
                <th>Azioni</th>
            </tr>
            <?php foreach ($prodotti as $p): ?>
                <tr>
                    <td><?= $p['id_prodotto'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= number_format($p['prezzo'], 2, ',', '.') ?> €</td>
                    <td><?= str_replace('_', ' ', $p['categoria']) ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="openModal(<?= $p['id_prodotto'] ?>)">Modifica</button>
                        <a href="admin_menu.php?delete=<?= $p['id_prodotto'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro di voler eliminare questo prodotto?')">Rimuovi</a>
                    </td>
                </tr>

                <div id="modal-<?= $p['id_prodotto'] ?>" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal(<?= $p['id_prodotto'] ?>)">&times;</span>
                        <h3>Modifica: <?= htmlspecialchars($p['nome']) ?></h3>
                        <form method="POST" action="admin_menu.php">
                            <input type="hidden" name="action" value="edit_product">
                            <input type="hidden" name="id" value="<?= $p['id_prodotto'] ?>">
                            <label>Nome:</label>
                            <input type="text" name="nome" value="<?= htmlspecialchars($p['nome']) ?>" required>
                            <label>Descrizione:</label>
                            <textarea name="descrizione"><?= htmlspecialchars($p['descrizione']) ?></textarea>
                            <label>Prezzo (€):</label>
                            <input type="number" step="0.01" name="prezzo" value="<?= $p['prezzo'] ?>" required>
                            <label>Categoria:</label>
                            <select name="categoria" required>
                                <option value="panino_predefinito" <?= $p['categoria'] == 'panino_predefinito' ? 'selected' : '' ?>>Panino predefinito</option>
                                <option value="pizzetta" <?= $p['categoria'] == 'pizzetta' ? 'selected' : '' ?>>Pizzetta</option>
                                <option value="bevanda" <?= $p['categoria'] == 'bevanda' ? 'selected' : '' ?>>Bevanda</option>
                                <option value="panino_componibile" <?= $p['categoria'] == 'panino_componibile' ? 'selected' : '' ?>>Panino componibile (base)</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Salva modifiche</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </table>
    </section>
</div>

<script src="js/admin_menu.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>