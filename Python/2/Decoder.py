from PIL import Image
import requests
from io import BytesIO
import StringIO

def decode_image_from_url(url):
    try:
        # Fetch the image from the URL
        response = requests.get(url)
        image = Image.open(BytesIO(response.content))

        # Prepare to decode the message
        decoded_bits = ''
        decoded_message = ''

        # Process each pixel in the image
        for y in range(image.size[1]):  # Use image.size for Python 2
            for x in range(image.size[0]):
                pixel = image.getpixel((x, y))

                # Assuming the image is an RGB image
                for color in pixel[:3]:  # Look at each color channel
                    decoded_bits += str(color & 1)  # Extract the LSB

                    # If we have 8 bits, convert them to a character
                    if len(decoded_bits) == 8:
                        decoded_character = chr(int(decoded_bits, 2))
                        decoded_bits = ''

                        # If the character is null, we've reached the end of the message
                        if decoded_character == '\x00':
                            return decoded_message
                        decoded_message += decoded_character

        return decoded_message
    except Exception as e:
        print "An error occurred:", e
        return None

# Example usage
url = 'YOUR_IMAGE_URL_HERE'
message = decode_image_from_url(url)
print "Decoded Message:", message
