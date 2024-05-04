# social_media_Integration
This project demonstrates integration with Instagram and LinkedIn APIs to share content asynchronously using PHP.

## Overview

The project consists of the following components:
- PHP scripts to interact with Instagram and LinkedIn APIs
- Python script to retrieve an image URL (example: `trii.py`)
- Dependencies managed via Composer
- Simple web interface to submit a caption and share an image

## Prerequisites

Before running this project, ensure you have the following:
- PHP installed on your local machine (version 7.4 or higher recommended)
- Composer installed to manage PHP dependencies
- Python installed to run the image retrieval script (`trii.py`)
- Access tokens and user IDs for Instagram and LinkedIn APIs

## Installation

1. Clone this repository to your local machine:
   ```bash
   git clone https://github.com/akansh2416/social-media-integration.git
   ```

2. Navigate to the project directory:
   ```bash
   cd social-media-integration
   ```

3. Install PHP dependencies using Composer:
   ```bash
   composer install
   ```

## Configuration

1. Obtain access tokens and user IDs for Instagram and LinkedIn APIs.
2. Update `process.php` with your credentials:
   - Replace `YOUR_USER_ID` and `YOUR_ACCESS_TOKEN` with your Instagram credentials.
   - Replace `YOUR_LINKEDIN_ACCESS_TOKEN` and `YOUR_LINKEDIN_USER_ID` with your LinkedIn credentials.

## Usage

1. Start a local PHP development server:
   ```bash
   php -S localhost:8000
   ```

2. Open your web browser and navigate to `http://localhost:8000`.

3. Fill out the form to submit a caption.

4. The script will run the Python script (`trii.py`) to retrieve an image URL and then share the image on Instagram and LinkedIn asynchronously.

## Directory Structure

- `src/`: Contains PHP classes and autoload script.
- `vendor/`: Contains Composer dependencies.
- `process.php`: Main script to handle form submission and share content.
- `trii.py`: Python script to retrieve an image URL.
- `README.md`: Project documentation.

## Contributing

Contributions are welcome! Feel free to submit issues or pull requests for any improvements or features you'd like to see.

## License

This project is licensed under the [MIT License](LICENSE).
```
