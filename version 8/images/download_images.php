<?php

$images = [
    'prague.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/f/f1/Prague_old_town_2016.jpg',
    'amsterdam.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/b/be/KeizersgrachtReguliersgrachtAmsterdam.jpg',
    'barcelona.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Barcelona_collage.JPG'
];

foreach ($images as $filename => $url) {
    $targetPath = __DIR__ . '/' . $filename;
    
    // Delete existing file if it's empty
    if (file_exists($targetPath) && filesize($targetPath) <= 1) {
        unlink($targetPath);
    }
    
    if (!file_exists($targetPath)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $imageContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $imageContent !== false) {
            file_put_contents($targetPath, $imageContent);
            echo "Downloaded {$filename}\n";
        } else {
            echo "Failed to download {$filename} (HTTP {$httpCode})\n";
        }
    } else {
        echo "{$filename} already exists and is not empty\n";
    }
}

echo "Done!\n";
