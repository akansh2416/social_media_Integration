<?php
require_once 'autoload.php';
use Instagram\User\Media;
use Instagram\User\MediaPublish;
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;

// Function to run Python script and capture the output
function runPythonScript($pythonScriptPath) {
    $command = "python $pythonScriptPath 2>&1"; // Capture both stdout and stderr
    $output = shell_exec($command);
    return trim($output);
}

$config = array(
    'user_id' => 'YOUR USER ID',
    'access_token' =>'YOUR_ACCESS_TOKEN',
);
//new token

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user input from the form
    $lol = isset($_POST['lol']) ? $_POST['lol'] : 'no caption';
    // Run the Python script to get the Imgur URL
    $pythonScriptPath = "trii.py"; //path of the python script
    $uploadedUrl = runPythonScript($pythonScriptPath);

    // Check if there was an error in running the Python script
    if ($uploadedUrl === 'ERROR') {
        echo 'Error running Python script.';
        // Handle the error as needed
    } else {
        // instantiate user media
        $media = new Media($config);

        $imageContainerParams = array(
            'caption' => $lol,
            'image_url' => $uploadedUrl,
        );

        // create image container
        $imageContainer = $media->create($imageContainerParams);

        // Check if the response has the expected "id" key
        if (isset($imageContainer['id'])) {
            $imageContainerId = $imageContainer['id'];

            // instantiate media publish
            $mediaPublish = new MediaPublish($config);

            // post our container with its contents to Instagram
            $publishedPost = $mediaPublish->create($imageContainerId);
            $successMessage = 'Posted with container id: ' . $imageContainerId;

            // Share on LinkedIn
            $link = 'YOUR_LINK_TO_SHARE';
            $access_token = 'YOUR LINKEDIN ACCESS TOKEN';
            $linkedin_id = 'YOUR LINKEDIN USER ID';
            $body = new \stdClass();
            $body->content = new \stdClass();
            $body->content->contentEntities[0] = new \stdClass();
            $body->text = new \stdClass();
            $body->content->contentEntities[0]->thumbnails[0] = new \stdClass();
            $body->content->contentEntities[0]->entityLocation = $link;
            $body->content->contentEntities[0]->thumbnails[0]->resolvedUrl = $uploadedUrl;
            $body->content->title = $lol;
            $body->owner = 'urn:li:person:' . $linkedin_id;
            $body->text->text = 'YOUR_POST_SHORT_SUMMARY';
            $body_json = json_encode($body, true);

            try {
                $client = new Client(['base_uri' => 'https://api.linkedin.com']);
                $response = $client->request('POST', '/v2/shares', [
                    'headers' => [
                        "Authorization" => "Bearer " . $access_token,
                        "Content-Type"  => "application/json",
                        "x-li-format"   => "json"
                    ],
                    'body' => $body_json,
                ]);

                if ($response->getStatusCode() == 201) {
                    echo 'Post is shared on LinkedIn successfully.';
                }
            } catch(Exception $e) {
                echo $e->getMessage(). ' for link '. $link;
            }
        } else {
            $errorMessage = 'Error creating image container. Response: ' . print_r($imageContainer, true);
            // Handle the error as needed
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column; /* Change to column */
        }

        form {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            margin-bottom: 20px; /* Add margin */
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Style for success and error messages */
        .success-message, .error-message {
            color: #4caf50; /* Green for success */
            margin-bottom: 20px;
        }

        .error-message {
            color: #f44336; /* Red for error */
        }
    </style>
</head>
<body>

<!-- Display success or error message -->
<?php if (isset($successMessage)): ?>
    <div class="success-message"><?php echo $successMessage; ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="error-message"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<!-- HTML form with a text input for user input -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="lol">Enter something for Caption:</label>
    <input type="text" id="lol" name="lol" required>
    <input type="submit" value="Submit">
</form>

</body>
</html>
