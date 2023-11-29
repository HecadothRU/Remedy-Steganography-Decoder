<?php
// Version: PHP 7.2 (REMAKE) v2
// Check if the 'image_url' parameter is provided
if (isset($_GET['image_url'])) {
    $imageUrl = $_GET['image_url'];

    // Validate the URL
    if (filter_var($imageUrl, FILTER_VALIDATE_URL) === FALSE) {
        die('Invalid URL');
    }

    // Fetch the image data
    $imageData = file_get_contents($imageUrl);
    if ($imageData === FALSE) {
        die('Could not fetch image');
    }

    // Save the image to a temporary file
    $tempFilePath = tempnam(sys_get_temp_dir(), 'image');
    file_put_contents($tempFilePath, $imageData);

    // Load the image
    $image = imagecreatefromstring($imageData);
    if ($image === FALSE) {
        die('Could not load image');
    }

    // Get image dimensions
    $width = imagesx($image);
    $height = imagesy($image);

    // Initialize variables to store the extracted binary data and decoding state
    $binaryData = '';
    $isMessageFound = false;

    // Loop through each pixel to extract the LSB
    for ($y = 0; $y < $height && !$isMessageFound; $y++) {
        for ($x = 0; $x < $width && !$isMessageFound; $x++) {
            // Get the color of the current pixel
            $rgb = imagecolorat($image, $x, $y);
            $colors = imagecolorsforindex($image, $rgb);

            // Extract the LSB from each color channel and append it to binary data
            $binaryData .= ($colors['red'] & 1) . ($colors['green'] & 1) . ($colors['blue'] & 1);

            // Decode every 8 bits (1 byte)
            if (strlen($binaryData) >= 8) {
                $byte = substr($binaryData, 0, 8);
                $binaryData = substr($binaryData, 8);
                $decodedCharacter = chr(bindec($byte));

                // Check for null character to detect end of message
                if ($decodedCharacter == "\0") {
                    $isMessageFound = true;
                }

                // Append the character to the decoded message
                if (!$isMessageFound) {
                    $decodedMessage .= $decodedCharacter;
                }
            }
        }
    }

    // Output the decoded message
    echo 'Decoded Message: ' . htmlspecialchars($decodedMessage, ENT_QUOTES | ENT_HTML5);

    // Cleanup: Destroy the image resource and remove the temporary file
    imagedestroy($image);
    unlink($tempFilePath);
} else {
    echo 'Please provide an image URL using the image_url parameter.';
}
?>
