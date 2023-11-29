# Steganography-Decoder
Steganography (IMAGE) Decoder Written in php 7.4 and maybe even some other languages / structures when i create them.

This script assumes that the hidden data is a text message encoded in binary form in the LSB of each color channel of the image.

This project is useful BUT simple since steganography methods can vary greatly
This example will be based on a simple steganography method where data is hidden in the least significant bit (LSB) of the image pixels.


> Fetches an image from a URL provided via a $_GET parameter.<br>
> Reads each pixel's color values from the image.<br>
> Extracts the least significant bit from each color channel (red, green, blue) of each pixel.<br>
> Converts the binary data extracted into a string.<br>

Please note:
This script is highly simplistic and may not work with complex steganography methods.
It's designed for demonstration purposes and might need significant adjustments for practical use.
Processing large images could lead to performance issues.
The script assumes that the hidden message is in plain text and uses a null character (\0) as a terminator. This may not be the case for all steganographic messages.
