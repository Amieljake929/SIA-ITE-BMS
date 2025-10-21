<?php
session_start();

// --- START OF CHANGES ---
// Define the new, larger dimensions for the image
$image_width = 360;
$image_height = 80;
// --- END OF CHANGES ---

// Generate a random 5-character string
$captcha_text = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);

// Store the captcha text in a session variable (case-insensitive for easier user input)
$_SESSION["captcha_text"] = strtolower($captcha_text);

// Create an image with the new dimensions
$image = imagecreatetruecolor($image_width, $image_height);

// Set up colors
$background_color = imagecolorallocate($image, 230, 240, 240); // Light gray background
$text_color = imagecolorallocate($image, 10, 20, 30);      // Dark text for contrast
$noise_color = imagecolorallocate($image, 150, 180, 180);   // Gray for noise elements

// Fill the background of the image with the new dimensions
imagefilledrectangle($image, 0, 0, $image_width, $image_height, $background_color);

// Add some random lines with the new dimensions
for ($i = 0; $i < 6; $i++) {
    imageline($image, 0, rand() % $image_height, $image_width, rand() % $image_height, $noise_color);
}

// Add more random dots (pixels) for the larger area
for ($i = 0; $i < 1000; $i++) {
    imagesetpixel($image, rand() % $image_width, rand() % $image_height, $noise_color);
}

// --- START OF CHANGES ---
// Calculate coordinates to center the text in the larger image
// Font size 5 has a width of approx. 9px and height of 15px per character
$text_width = imagefontwidth(5) * strlen($captcha_text);
$text_height = imagefontheight(5);

$x_pos = ($image_width - $text_width) / 2;
$y_pos = ($image_height - $text_height) / 2;

// Add the text to the image using the calculated centered position
imagestring($image, 5, $x_pos, $y_pos, $captcha_text, $text_color);
// --- END OF CHANGES ---

// Set the content type header to image/png and output the image
header("Content-type: image/png");
imagepng($image);

// Clean up memory
imagedestroy($image);
?>

