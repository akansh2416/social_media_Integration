import requests
import base64
from tkinter import filedialog

def upload_image_to_imgur():
    # Set API endpoint and headers
    url = "https://api.imgur.com/3/image"
    headers = {"Authorization": "Client-ID 2edb68fbd725704"}

    # Read image file and encode as base64
    file_path = filedialog.askopenfilename(initialdir="/", title="Select file",
                                            filetypes=(("image files", "*.png;*.jpg;*.jpeg;*.gif"), ("all files", "*.*")))
    if file_path:
        with open(file_path, "rb") as file:
            data = file.read()
            base64_data = base64.b64encode(data).decode()

        # Upload image to Imgur and get URL
        response = requests.post(url, headers=headers, data={"image": base64_data})

        # Check if the request was successful
        if response.status_code == 200:
            uploaded_url = response.json()["data"]["link"]
            return uploaded_url
        else:
            print("Error uploading image to Imgur. Status code:", response.status_code)
            return 'ERROR'

if __name__ == "__main__":
    imgur_url = upload_image_to_imgur()
    print(imgur_url)
