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
        $nome = trim($_POST['nome']);
        $descrizione = trim($_POST['descrizione']);
        $prezzo = floatval($_POST['prezzo']);
        $categoria = $_POST['categoria'];
        $path_immagine = 'img/default.jpg';
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] == 0) {
            $upload_dir = __DIR__ . '/img/';
            $filename = uniqid() . '-' . basename($_FILES['immagine']['name']);
            if (move_uploaded_file($_FILES['immagine']['tmp_name'], $upload_dir . $filename)) {
                $path_immagine = 'img/' . $filename;
            }
        }
        $stmt = $conn->prepare("INSERT INTO Prodotti (nome, descrizione, prezzo, categoria, path_immagine) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $nome, $descrizione, $prezzo, $categoria, $path_immagine);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit_product') {
        $id = intval($_POST['id']);
        $path_immagine = $_POST['immagine_esistente'];
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] == 0) {
            $upload_dir = __DIR__ . '/img/';
            $filename = uniqid() . '-' . basename($_FILES['immagine']['name']);
            if (move_uploaded_file($_FILES['immagine']['tmp_name'], $upload_dir . $filename)) {
                $path_immagine = 'img/' . $filename;
            }
        }
        $stmt = $conn->prepare("UPDATE Prodotti SET nome=?, descrizione=?, prezzo=?, categoria=?, path_immagine=? WHERE id_prodotto=?");
        $stmt->bind_param("ssdssi", $_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_POST['categoria'], $path_immagine, $id);
        $stmt->execute();
    } elseif ($_POST['action'] === 'add_ingredient') {
        $stmt = $conn->prepare("INSERT INTO Ingredienti (nome, categoria_ingrediente) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['nome'], $_POST['categoria']);
        $stmt->execute();
    } elseif ($_POST['action'] === 'edit_ingredient') {
        $stmt = $conn->prepare("UPDATE Ingredienti SET nome=?, categoria_ingrediente=? WHERE id_ingrediente=?");
        $stmt->bind_param("ssi", $_POST['nome'], $_POST['categoria'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: admin_menu.php");
    exit();
}

if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $stmt = $conn->prepare("DELETE FROM Prodotti WHERE id_prodotto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_menu.php");
    exit();
}

if (isset($_GET['delete_ingredient'])) {
    $id = intval($_GET['delete_ingredient']);
    $stmt = $conn->prepare("DELETE FROM Ingredienti WHERE id_ingrediente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_menu.php");
    exit();
}

$prodotti = $conn->query("SELECT * FROM Prodotti ORDER BY categoria, nome")->fetch_all(MYSQLI_ASSOC);
$ingredienti = $conn->query("SELECT * FROM Ingredienti ORDER BY categoria_ingrediente, nome")->fetch_all(MYSQLI_ASSOC);

$page_title = "Gestione Menu";
include_once __DIR__ . '/../templates/header.php';
?>
<link rel="stylesheet" href="css/admin.css">

<div class="dashboard-container">
    <h1>Gestione Menu e Ingredienti</h1>
    <a href="admin.php" class="btn-back-dashboard">⬅ Torna alla Dashboard</a>

    <section class="card">
        <h2>Aggiungi Nuovo Prodotto</h2>
        <form method="POST" action="admin_menu.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_product">
            <div class="form-row">
                <div class="form-group flex-3"><label>Nome:</label><input type="text" name="nome" required></div>
                <div class="form-group flex-1"><label>Prezzo (€):</label><input type="number" step="0.01" name="prezzo" required></div>
            </div>
            <div class="form-group"><label>Descrizione:</label><textarea name="descrizione" rows="2"></textarea></div>
            <div class="form-row">
                <div class="form-group flex-1"><label>Categoria:</label><select name="categoria" required>
                        <option value="panino_predefinito">Panino</option>
                        <option value="pizzetta">Pizzetta</option>
                        <option value="bevanda">Bevanda</option>
                    </select></div>
                <div class="form-group flex-1"><label>Immagine:</label><input type="file" name="immagine" accept="image/*"></div>
            </div>
            <button type="submit" class="btn btn-add">Aggiungi Prodotto</button>
        </form>
    </section>

    <section class="card">
        <h2>Elenco Prodotti</h2>
        <table>
            <tbody>
                <?php foreach ($prodotti as $p): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($p['path_immagine']); ?>" alt="" class="product-thumbnail"></td>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= number_format($p['prezzo'], 2, ',', '.') ?> €</td>
                        <td><?= str_replace('_', ' ', $p['categoria']) ?></td>
                        <td>
                            <button class="btn btn-edit" data-modal-id="modal-prod-<?= $p['id_prodotto'] ?>">Modifica</button>
                            <a href="admin_menu.php?delete_product=<?= $p['id_prodotto'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro?')">Rimuovi</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Aggiungi Nuovo Ingrediente</h2>
        <form method="POST" action="admin_menu.php">
            <input type="hidden" name="action" value="add_ingredient">
            <div class="form-row">
                <div class="form-group flex-2"><label>Nome Ingrediente:</label><input type="text" name="nome" required></div>
                <div class="form-group flex-1"><label>Categoria:</label><select name="categoria" required>
                        <option value="pane">Pane</option>
                        <option value="proteina">Proteina</option>
                        <option value="contorno">Contorno</option>
                        <option value="salsa">Salsa</option>
                    </select></div>
            </div>
            <button type="submit" class="btn btn-add">Aggiungi Ingrediente</button>
        </form>
    </section>

    <section class="card">
        <h2>Elenco Ingredienti</h2>
        <table>
            <tbody>
                <?php foreach ($ingredienti as $ing): ?>
                    <tr>
                        <td><?= $ing['id_ingrediente'] ?></td>
                        <td><?= htmlspecialchars($ing['nome']) ?></td>
                        <td><?= ucfirst($ing['categoria_ingrediente']) ?></td>
                        <td>
                            <button class="btn btn-edit" data-modal-id="modal-ing-<?= $ing['id_ingrediente'] ?>">Modifica</button>
                            <a href="admin_menu.php?delete_ingredient=<?= $ing['id_ingrediente'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro?')">Rimuovi</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php foreach ($prodotti as $p): ?>
    <div id="modal-prod-<?= $p['id_prodotto'] ?>" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Modifica: <?= htmlspecialchars($p['nome']) ?></h3>
            <form method="POST" action="admin_menu.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="id" value="<?= $p['id_prodotto'] ?>">
                <input type="hidden" name="immagine_esistente" value="<?= htmlspecialchars($p['path_immagine']) ?>">
                <div class="form-group"><label>Nome:</label><input type="text" name="nome" value="<?= htmlspecialchars($p['nome']) ?>" required></div>
                <div class="form-group"><label>Descrizione:</label><textarea name="descrizione"><?= htmlspecialchars($p['descrizione']) ?></textarea></div>
                <div class="form-group"><label>Prezzo (€):</label><input type="number" step="0.01" name="prezzo" value="<?= $p['prezzo'] ?>" required></div>
                <div class="form-group"><label>Categoria:</label><select name="categoria" required>
                        <option value="panino_predefinito" <?= $p['categoria'] == 'panino_predefinito' ? 'selected' : '' ?>>Panino</option>
                        <option value="pizzetta" <?= $p['categoria'] == 'pizzetta' ? 'selected' : '' ?>>Pizzetta</option>
                        <option value="bevanda" <?= $p['categoria'] == 'bevanda' ? 'selected' : '' ?>>Bevanda</option>
                    </select></div>
                <div class="form-group"><label>Nuova Immagine (lascia vuoto per non modificare):</label><input type="file" name="immagine" accept="image/*"></div>
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<?php foreach ($ingredienti as $ing): ?>
    <div id="modal-ing-<?= $ing['id_ingrediente'] ?>" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Modifica: <?= htmlspecialchars($ing['nome']) ?></h3>
            <form method="POST" action="admin_menu.php">
                <input type="hidden" name="action" value="edit_ingredient">
                <input type="hidden" name="id" value="<?= $ing['id_ingrediente'] ?>">
                <div class="form-group"><label>Nome:</label><input type="text" name="nome" value="<?= htmlspecialchars($ing['nome']) ?>" required></div>
                <div class="form-group"><label>Categoria:</label><select name="categoria" required>
                        <option value="pane" <?= $ing['categoria_ingrediente'] == 'pane' ? 'selected' : '' ?>>Pane</option>
                        <option value="proteina" <?= $ing['categoria_ingrediente'] == 'proteina' ? 'selected' : '' ?>>Proteina</option>
                        <option value="contorno" <?= $ing['categoria_ingrediente'] == 'contorno' ? 'selected' : '' ?>>Contorno</option>
                        <option value="salsa" <?= $ing['categoria_ingrediente'] == 'salsa' ? 'selected' : '' ?>>Salsa</option>
                    </select></div>
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script src="js/admin_menu.js" defer></script>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>