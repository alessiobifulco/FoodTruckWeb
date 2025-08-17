<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'venditore') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_ingredient') {
        $stmt = $conn->prepare("INSERT INTO Ingredienti (nome, categoria_ingrediente) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['nome'], $_POST['categoria']);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit_ingredient') {
        $stmt = $conn->prepare("UPDATE Ingredienti SET nome=?, categoria_ingrediente=? WHERE id_ingrediente=?");
        $stmt->bind_param("ssi", $_POST['nome'], $_POST['categoria'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: admin_ingredients.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Ingredienti WHERE id_ingrediente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_ingredients.php");
    exit();
}

$result = $conn->query("SELECT * FROM Ingredienti ORDER BY categoria_ingrediente, nome");
$ingredienti = $result->fetch_all(MYSQLI_ASSOC);

$page_title = "Gestione Ingredienti";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Gestione Ingredienti</h1>
    <a href="admin.php" class="btn-back-dashboard">⬅ Torna alla Dashboard</a>

    <section class="card">
        <h2>Aggiungi Nuovo Ingrediente</h2>
        <form method="POST" action="admin_ingredients.php">
            <input type="hidden" name="action" value="add_ingredient">
            <div class="form-row">
                <div class="form-group flex-2">
                    <label>Nome Ingrediente:</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group flex-1">
                    <label>Categoria:</label>
                    <select name="categoria" required>
                        <option value="pane">Pane</option>
                        <option value="proteina">Proteina</option>
                        <option value="contorno">Contorno</option>
                        <option value="salsa">Salsa</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-add">Aggiungi Ingrediente</button>
        </form>
    </section>

    <section class="card">
        <h2>Elenco Ingredienti</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredienti as $ing): ?>
                    <tr>
                        <td><?= $ing['id_ingrediente'] ?></td>
                        <td><?= htmlspecialchars($ing['nome']) ?></td>
                        <td><?= ucfirst($ing['categoria_ingrediente']) ?></td>
                        <td>
                            <button class="btn btn-edit" data-modal-id="modal-ing-<?= $ing['id_ingrediente'] ?>">Modifica</button>
                            <a href="admin_ingredients.php?delete=<?= $ing['id_ingrediente'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro?')">Rimuovi</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php foreach ($ingredienti as $ing): ?>
    <div id="modal-ing-<?= $ing['id_ingrediente'] ?>" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Modifica: <?= htmlspecialchars($ing['nome']) ?></h3>
            <form method="POST" action="admin_ingredients.php">
                <input type="hidden" name="action" value="edit_ingredient">
                <input type="hidden" name="id" value="<?= $ing['id_ingrediente'] ?>">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($ing['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="categoria" required>
                        <option value="pane" <?= $ing['categoria_ingrediente'] == 'pane' ? 'selected' : '' ?>>Pane</option>
                        <option value="proteina" <?= $ing['categoria_ingrediente'] == 'proteina' ? 'selected' : '' ?>>Proteina</option>
                        <option value="contorno" <?= $ing['categoria_ingrediente'] == 'contorno' ? 'selected' : '' ?>>Contorno</option>
                        <option value="salsa" <?= $ing['categoria_ingrediente'] == 'salsa' ? 'selected' : '' ?>>Salsa</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script src="js/admin_ingredients.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>