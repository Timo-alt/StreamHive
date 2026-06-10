<?php
    require_once __DIR__ . '/../app/models/videoModel.php';
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
      <div class="page-container">
    
    <a href="index.php" class="btn-nav-left">&larr; Terug naar home</a>
    
    <header class="page-header">
        <span class="subtitle">Streamhive</span>
    </header>
  
        <header class="page-header">
            <h1>Upload video</h1>
        </header>

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