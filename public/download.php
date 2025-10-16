<?php
// download.php

function getVideoIdFromUrl($url)
{
    // Extract video ID using regex
    if (preg_match('/video\/(\d+)/', $url, $matches)) {
        return $matches[1];
    }
    return false;
}

function getTikTokVideoUrl($html)
{
    // Try to find the JSON data that contains video information
    if (preg_match('/"playAddr":"([^"]+)"/', $html, $matches)) {
        $video_url = str_replace(['\u002F', '\\u002F'], '/', $matches[1]);
        return $video_url;
    }
    return false;
}

function getTikTokData($url)
{
    $ch = curl_init();

    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEJAR => 'cookies.txt',
        CURLOPT_COOKIEFILE => 'cookies.txt'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return $response;
    }

    return false;
}

function downloadTikTokVideo($video_url)
{
    $ch = curl_init();

    $headers = [
        'Range: bytes=0-',
        'Referer: https://www.tiktok.com/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => $video_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_COOKIEJAR => 'cookies.txt',
        CURLOPT_COOKIEFILE => 'cookies.txt'
    ]);

    $video_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 || $http_code == 206) { // 206 is for partial content
        return $video_data;
    }

    return false;
}

// Debug function
function debug_to_file($data, $append = true)
{
    $mode = $append ? FILE_APPEND : 0;
    file_put_contents('debug.log', date('Y-m-d H:i:s') . ' - ' . print_r($data, true) . "\n", $mode);
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['tiktok_url'])) {
    $tiktok_url = trim($_POST['tiktok_url']);

    // Debug
    debug_to_file('Processing URL: ' . $tiktok_url);

    // Get video ID
    $video_id = getVideoIdFromUrl($tiktok_url);

    if ($video_id) {
        // Get the HTML content
        $html_content = getTikTokData($tiktok_url);

        if ($html_content) {
            // Debug
            debug_to_file('HTML content length: ' . strlen($html_content));

            // Get video URL
            $video_url = getTikTokVideoUrl($html_content);

            if ($video_url) {
                // Debug
                debug_to_file('Found video URL: ' . $video_url);

                // Download video
                $video_data = downloadTikTokVideo($video_url);

                if ($video_data) {
                    // Debug
                    debug_to_file('Video data length: ' . strlen($video_data));

                    // Set headers for video download
                    header('Content-Type: video/mp4');
                    header('Content-Disposition: attachment; filename="tiktok_' . $video_id . '.mp4"');
                    header('Content-Length: ' . strlen($video_data));
                    header('Accept-Ranges: bytes');
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Pragma: no-cache');
                    header('Expires: 0');

                    // Output video data
                    echo $video_data;
                    exit;
                } else {
                    debug_to_file('Failed to download video data');
                    $error = "Failed to download video data. Please try again.";
                }
            } else {
                debug_to_file('Could not find video URL in HTML content');
                $error = "Could not find video URL. The video might be private or unavailable.";
            }
        } else {
            debug_to_file('Failed to get HTML content');
            $error = "Failed to access TikTok page. Please try again.";
        }
    } else {
        debug_to_file('Invalid TikTok URL format');
        $error = "Invalid TikTok URL format. Please make sure you're using a valid TikTok video URL.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok Video Downloader</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0f2f5;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #fe2c55;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #fe2c55;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #e62a4d;
        }

        .error {
            color: #fe2c55;
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            background-color: #ffe6e6;
            border-radius: 4px;
        }

        .instructions {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .debug-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>TikTok Video Downloader</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="tiktok_url" placeholder="Paste TikTok video URL here"
                    value="<?php echo isset($_POST['tiktok_url']) ? htmlspecialchars($_POST['tiktok_url']) : ''; ?>"
                    required>
            </div>
            <button type="submit">Download Video</button>
        </form>

        <div class="instructions">
            <h3>How to use:</h3>
            <ol>
                <li>Open TikTok in your browser</li>
                <li>Go to the video you want to download</li>
                <li>Copy the entire URL from your browser's address bar</li>
                <li>Paste the URL in the input field above</li>
                <li>Click the Download Video button</li>
            </ol>
            <p><strong>URL Format Example:</strong><br>
                https://www.tiktok.com/@username/video/1234567890</p>
            <p><strong>Note:</strong> This tool works with public TikTok videos only.</p>
        </div>
    </div>
</body>

</html>