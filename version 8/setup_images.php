<?php
// Create images directory if it doesn't exist
$imageDir = __DIR__ . '/images';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Sample image data
$images = [
    'paris.jpg' => 'Experience the Eiffel Tower and charming streets of Paris',
    'rome.jpg' => 'The historic Colosseum and Roman architecture',
    'barcelona.jpg' => 'Stunning Sagrada Familia and Mediterranean beaches',
    'amsterdam.jpg' => 'Beautiful canals and historic Dutch architecture',
    'prague.jpg' => 'Medieval architecture and the Charles Bridge'
];

// Copy sample images
foreach ($images as $filename => $description) {
    // Source can be either from root images dir or trips subdir
    $possibleSources = [
        __DIR__ . '/images/' . $filename,
        __DIR__ . '/images/trips/' . $filename
    ];
    
    $targetPath = $imageDir . '/' . $filename;
    
    // Skip if target already exists
    if (file_exists($targetPath)) {
        echo "Image {$filename} already exists.\n";
        continue;
    }
    
    // Try to copy from possible source locations
    $copied = false;
    foreach ($possibleSources as $sourcePath) {
        if (file_exists($sourcePath)) {
            copy($sourcePath, $targetPath);
            echo "Copied {$filename} to images directory.\n";
            $copied = true;
            break;
        }
    }
    
    // If image wasn't found, copy a default image
    if (!$copied) {
        $defaultImage = __DIR__ . '/images/default.jpg';
        if (file_exists($defaultImage)) {
            copy($defaultImage, $targetPath);
            echo "Copied default image for {$filename}.\n";
        } else {
            echo "Warning: Could not find image for {$filename}.\n";
        }
    }
}

echo "Image setup complete.\n";
