<?php
    require_once __DIR__ . '/../app/models/videoModel.php';
    require_once __DIR__ . '/../app/models/userModel.php';

    // Laat de video zien op de pagina 
    if (isset($conn)) {
    try {
        $fetchVideos = $conn->query("SELECT * FROM videos ORDER BY id DESC");
        echo "<div class='video-grid'>";
        
        while($row = $fetchVideos->fetch(PDO::FETCH_ASSOC)){
            $location = $row['filename']; 
            $name = $row['title']; 
            
            echo "<div class='video-item'>
               <video src='".$location."' controls width='320px' height='320px'></video>     
               <br>
               <span>".$name."</span>
            </div>";
        }
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Fout bij ophalen video's: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red;'>Fout: Databaseverbinding (\$conn) is niet beschikbaar. Controleer core/database.php</p>";
}
?>
<title>Home</title>
<link rel="stylesheet" href="assets/home.css">

<div class="sidebar">
    <a href="upload_videos.php" class="btn-nav-left">Video Uploaden</a>
</div>