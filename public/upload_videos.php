<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inladen van de OOP componenten
require_once __DIR__ . '/../core/Database.php'; 
require_once __DIR__ . '/../app/models/videoModel.php';

// Check of de uploadknop is ingedrukt
if (isset($_POST['but_upload'])) {
    // Beveiliging: zorg dat de user_id in de sessie staat
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Fout: Je moet ingelogd zijn om een video te uploaden.";
    } else {
        // 1. Initialiseer de databaseklasse en maak verbinding
        $database = new Database();
        $conn = $database->connect(); 

        // 2. Initialiseer de uploader en geef de actieve connectie mee
        $uploader = new VideoModel($conn);;
        
        // 3. Voer de upload uit en sla de melding op in de sessie
        $_SESSION['message'] = $uploader->upload($_FILES['file'], $_POST, (int)$_SESSION['user_id']);
    }
    
    // Stuur de gebruiker door naar index.php na de upload actie
    header('location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>StreamHive - Creator Studio</title>
    <link rel="stylesheet" href="assets/upload_video.css">
</head>
<body>
    <div class="page-container">
        
        <a href="index.php" class="btn-nav-left">&larr; Terug naar home</a>
        
        <header class="page-header">
            <span class="subtitle">Streamhive</span>
        </header>
      
        <header class="page-header">
            <h1>Upload video</h1>
        </header>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert-message" style="padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border-radius: 4px;">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); // Direct verwijderen zodat de melding niet blijft staan
                ?>
            </div>
        <?php endif; ?>

        <section class="upload-section">
            <form action="" method="post" enctype="multipart/form-data">
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="video_title">Video Titel</label>
                        <input type="text" name="video_title" id="video_title" placeholder="Geef je video een naam..." required>
                    </div>

                    <div class="input-group">
                        <label for="file">Kies videobestand</label>
                        <input type="file" name="file" id="file" accept="video/*" required>
                    </div>
                </div>

                <button type="submit" name="but_upload" class="upload-button">Video uploaden</button>
                
            </form>
        </section>

    </div>
</body>
</html>