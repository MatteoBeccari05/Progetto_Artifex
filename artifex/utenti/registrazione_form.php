<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style/style_access.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Registrazione Utente</h2>

    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <form action="registrazione.php" method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="cognome" class="form-label">Cognome</label>
            <input type="text" class="form-control" id="cognome" name="cognome" required value="<?php echo isset($_POST['cognome']) ? htmlspecialchars($_POST['cognome']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="nazionalita" class="form-label">Nazionalità</label>
            <input type="text" class="form-control" id="nazionalita" name="nazionalita" required value="<?php echo isset($_POST['nazionalita']) ? htmlspecialchars($_POST['nazionalita']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="lingua_base" class="form-label">Lingua Base</label>
            <select class="form-control" id="lingua_base" name="lingua_base" required>
                <?php
                $lingue = ['italiano', 'inglese', 'spagnolo', 'francese', 'tedesco', 'portoghese', 'russo', 'cinese', 'giapponese', 'arabo'];
                $selected_lingua = isset($_POST['lingua_base']) ? $_POST['lingua_base'] : '';

                foreach ($lingue as $lingua) {
                    $selected = ($lingua == $selected_lingua) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($lingua) . '" ' . $selected . '>' . ucfirst(htmlspecialchars($lingua)) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Telefono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <br>
        <button type="submit" class="add-to-cart-btn">Registrati</button>
        <button type="button" class="btnhome" onclick="window.location.href='../pages/home.php'">Torna alla home</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>