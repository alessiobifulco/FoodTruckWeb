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
                <div class="form-group flex-3"><label for="add_prod_nome">Nome:</label><input type="text" name="nome" id="add_prod_nome" required></div>
                <div class="form-group flex-1"><label for="add_prod_prezzo">Prezzo (€):</label><input type="number" step="0.01" name="prezzo" id="add_prod_prezzo" required></div>
            </div>
            <div class="form-group"><label for="add_prod_desc">Descrizione:</label><textarea name="descrizione" id="add_prod_desc" rows="2"></textarea></div>
            <div class="form-row">
                <div class="form-group flex-1"><label for="add_prod_cat">Categoria:</label><select name="categoria" id="add_prod_cat" required>
                        <option value="panino_predefinito">Panino</option>
                        <option value="pizzetta">Pizzetta</option>
                        <option value="bevanda">Bevanda</option>
                    </select></div>
                <div class="form-group flex-1"><label for="add_prod_img">Immagine:</label><input type="file" name="immagine" id="add_prod_img" accept="image/*"></div>
            </div>
            <button type="submit" class="btn btn-add">Aggiungi Prodotto</button>
        </form>
    </section>

    <section class="card">
        <h2>Elenco Prodotti</h2>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th data-label="Nome">Nome</th>
                    <th data-label="Prezzo">Prezzo</th>
                    <th data-label="Categoria">Categoria</th>
                    <th data-label="Azioni">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prodotti as $p): ?>
                    <tr>
                        <td data-label="Immagine"><img src="<?php echo htmlspecialchars($p['path_immagine']); ?>" alt="Immagine di <?= htmlspecialchars($p['nome']) ?>" class="product-thumbnail"></td>
                        <td data-label="Nome"><?= htmlspecialchars($p['nome']) ?></td>
                        <td data-label="Prezzo"><?= number_format($p['prezzo'], 2, ',', '.') ?> €</td>
                        <td data-label="Categoria"><?= str_replace('_', ' ', $p['categoria']) ?></td>
                        <td data-label="Azioni">
                            <button class="btn btn-edit" data-modal-id="modal-prod-<?= $p['id_prodotto'] ?>">Modifica</button>
                            <a href="admin_menu.php?delete_product=<?= $p['id_prodotto'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro di voler rimuovere questo prodotto?')">Rimuovi</a>
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
                <div class="form-group flex-2"><label for="add_ing_nome">Nome Ingrediente:</label><input type="text" name="nome" id="add_ing_nome" required></div>
                <div class="form-group flex-1"><label for="add_ing_categoria">Categoria:</label><select name="categoria" id="add_ing_categoria" required>
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
            <thead>
                <tr>
                    <th data-label="ID">ID</th>
                    <th data-label="Nome">Nome</th>
                    <th data-label="Categoria">Categoria</th>
                    <th data-label="Azioni">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredienti as $ing): ?>
                    <tr>
                        <td data-label="ID"><?= $ing['id_ingrediente'] ?></td>
                        <td data-label="Nome"><?= htmlspecialchars($ing['nome']) ?></td>
                        <td data-label="Categoria"><?= ucfirst($ing['categoria_ingrediente']) ?></td>
                        <td data-label="Azioni">
                            <button class="btn btn-edit" data-modal-id="modal-ing-<?= $ing['id_ingrediente'] ?>">Modifica</button>
                            <a href="admin_menu.php?delete_ingredient=<?= $ing['id_ingrediente'] ?>" class="btn btn-remove" onclick="return confirm('Sei sicuro di voler rimuovere questo ingrediente?')">Rimuovi</a>
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
                <div class="form-group"><label for="edit_prod_nome_<?= $p['id_prodotto'] ?>">Nome:</label><input type="text" name="nome" id="edit_prod_nome_<?= $p['id_prodotto'] ?>" value="<?= htmlspecialchars($p['nome']) ?>" required></div>
                <div class="form-group"><label for="edit_prod_desc_<?= $p['id_prodotto'] ?>">Descrizione:</label><textarea name="descrizione" id="edit_prod_desc_<?= $p['id_prodotto'] ?>"><?= htmlspecialchars($p['descrizione']) ?></textarea></div>
                <div class="form-group"><label for="edit_prod_prezzo_<?= $p['id_prodotto'] ?>">Prezzo (€):</label><input type="number" step="0.01" name="prezzo" id="edit_prod_prezzo_<?= $p['id_prodotto'] ?>" value="<?= $p['prezzo'] ?>" required></div>
                <div class="form-group"><label for="edit_prod_cat_<?= $p['id_prodotto'] ?>">Categoria:</label><select name="categoria" id="edit_prod_cat_<?= $p['id_prodotto'] ?>" required>
                        <option value="panino_predefinito" <?= $p['categoria'] == 'panino_predefinito' ? 'selected' : '' ?>>Panino</option>
                        <option value="pizzetta" <?= $p['categoria'] == 'pizzetta' ? 'selected' : '' ?>>Pizzetta</option>
                        <option value="bevanda" <?= $p['categoria'] == 'bevanda' ? 'selected' : '' ?>>Bevanda</option>
                    </select></div>
                <div class="form-group"><label for="edit_prod_img_<?= $p['id_prodotto'] ?>">Nuova Immagine (lascia vuoto per non modificare):</label><input type="file" name="immagine" id="edit_prod_img_<?= $p['id_prodotto'] ?>" accept="image/*"></div>
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
                <div class="form-group"><label for="edit_ing_nome_<?= $ing['id_ingrediente'] ?>">Nome:</label><input type="text" name="nome" id="edit_ing_nome_<?= $ing['id_ingrediente'] ?>" value="<?= htmlspecialchars($ing['nome']) ?>" required></div>
                <div class="form-group"><label for="edit_ing_cat_<?= $ing['id_ingrediente'] ?>">Categoria:</label><select name="categoria" id="edit_ing_cat_<?= $ing['id_ingrediente'] ?>" required>
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