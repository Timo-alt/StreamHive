<?php
    require_once __DIR__ . '/../app/models/userModel.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren bij StreamHive</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="header-section">
            <span class="subtitle">NIEUW ACCOUNT</span>
            <h1>Registreer bij StreamHive</h1>
        </div>

        <!-- Toon eventuele fouten of succesmeldingen -->
        <?php echo $feedbackMessage; ?>

        <form class="login-form" method="POST" action="register.php">
            <!-- Dit vertelt PHP dat dit het registratie-formulier is -->
            <input type="hidden" name="action" value="register">

            <div class="input-group">
                <label for="username">Gebruikersnaam</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="confirm-password">Wachtwoord herhalen</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>

            <button type="submit" class="login-button">Registreren</button>

            <div class="footer-links">
                <span>Heb je al een account?</span>
                <a href="login.php">Inloggen</a>
            </div>
        </form>
    </div>
</body>
</html>