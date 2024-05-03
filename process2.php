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
    'user_id' => 'YOUR INSTAGRAM USER ID',
    'access_token' => 'YOUR INSTGRAM ACCESS TOKEN',
);

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
            $linkedin_id = 'LINKEDIN USERID';
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
                    $successMessage .= ' Post is shared on LinkedIn successfully.';
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
