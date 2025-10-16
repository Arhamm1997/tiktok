# TikTok Video Downloader

This project is a simple PHP-based TikTok video downloader that allows users to download public TikTok videos by providing the video URL.

## Features

- Extracts video IDs from TikTok URLs.
- Fetches video data from TikTok.
- Downloads videos in MP4 format.
- User-friendly interface for inputting TikTok URLs.

## Project Structure

```
tiktok-downloader-php
├── public
│   └── download.php       # PHP script for downloading TikTok videos
├── vercel.json            # Vercel configuration file
└── README.md              # Project documentation
```

## Installation

1. Clone the repository to your local machine:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd tiktok-downloader-php
   ```

3. Ensure you have a PHP server running. You can use built-in PHP server for testing:
   ```
   php -S localhost:8000 -t public
   ```

4. Open your browser and go to `http://localhost:8000/download.php` to access the TikTok video downloader.

## Usage

1. Open TikTok in your browser and navigate to the video you want to download.
2. Copy the entire URL from your browser's address bar.
3. Paste the URL into the input field on the downloader page.
4. Click the "Download Video" button to start the download.

## Deployment on Vercel

To deploy this project on Vercel, follow these steps:

1. Create a `vercel.json` file in the root directory with the necessary configuration.
2. Push your code to a Git repository (GitHub, GitLab, etc.).
3. Connect your repository to Vercel and deploy.

## Note

This tool works with public TikTok videos only. Ensure that you have the right to download the videos you are accessing.