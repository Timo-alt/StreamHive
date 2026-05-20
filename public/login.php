<?php
    require_once __DIR__ . '/../app/models/userModel.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen bij StreamHive</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="header-section">
            <span class="subtitle">WELKOM TERUG</span>
            <h1>Log in bij StreamHive</h1>
        </div>

        <!-- Toon eventuele fouten of succesmeldingen -->
        <?php echo $feedbackMessage; ?>

        <form class="login-form" method="POST" action="login.php">
            <!-- Dit vertelt PHP dat dit het login-formulier is -->
            <input type="hidden" name="action" value="login">

            <div class="input-group">
                <label for="username">Gebruikersnaam of e-mail</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-button">Inloggen</button>

            <div class="footer-links">
                <span>Nog geen account?</span>
                <a href="register.php">Registreren</a>
            </div>
        </form>
    </div>
</body>
</html>


