<?php
// Version: PHP 8
// Check if the 'image_url' parameter is provided
if (isset($_GET['image_url'])) {
    $imageUrl = $_GET['image_url'];

    // Validate the URL
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        die('Invalid URL');
    }

    // Fetch the image data
    $imageData = file_get_contents($imageUrl);
    if ($imageData === false) {
        die('Could not fetch image');
    }

    // Create an image from the string data
    $image = imagecreatefromstring($imageData);
    if (!$image) {
        die('Could not create image');
    }

    // Get image dimensions
    $width = imagesx($image);
    $height = imagesy($image);

    // Initialize a variable to store the extracted binary data
    $binaryData = '';

    // Loop through each pixel to extract the LSB
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            // Get the color of the current pixel
            $rgb = imagecolorat($image, $x, $y);
            $colors = imagecolorsforindex($image, $rgb);

            // Extract the LSB from each color channel
            $binaryData .= ($colors['red'] & 1) . ($colors['green'] & 1) . ($colors['blue'] & 1);
        }
    }

    // Convert the binary data to a string
    $decodedMessage = '';
    for ($i = 0; $i < strlen($binaryData); $i += 8) {
        $byte = substr($binaryData, $i, 8);
        if (strlen($byte) < 8) {
            break; // Avoids padding issues
        }
        $decodedCharacter = chr(bindec($byte));
        $decodedMessage .= $decodedCharacter;

        // Optional: Stop if the decoder finds the null character
        if ($decodedCharacter === "\0") {
            break;
        }
    }

    // Output the decoded message
    echo 'Decoded Message: ' . htmlspecialchars($decodedMessage, ENT_QUOTES | ENT_HTML5);

    // Cleanup: Destroy the image resource
    imagedestroy($image);
} else {
    echo 'Please provide an image URL using the image_url parameter.';
}
?>
