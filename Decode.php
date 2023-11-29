<?php
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
        $decodedCharacter = chr(bindec($byte));
        $decodedMessage .= $decodedCharacter;

        // Optional: Stop if the decoder finds the null character
        if ($decodedCharacter == "\0") {
            break;
        }
    }

    // Output the decoded message
    echo 'Decoded Message: ' . $decodedMessage;

    // Cleanup: Destroy the image resource and remove the temporary file
    imagedestroy($image);
    unlink($tempFilePath);
} else {
    echo 'Please provide an image URL using the image_url parameter.';
}
?>
