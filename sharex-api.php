<?php

/*
  ********************
  ShareX-API
  By Senator Modified by Nathan Strobbe
  ********************
  
  MIT License

  Copyright (c) 2020 Nathan Strobbe
  
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:
  
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
  
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
*/

// NOTE: REMEMBER TO SET upload_max_filesize in the php.ini!

// Set Our Return data type to text, (to prevent any sort of XSS or php shell execution exploitation)
header('Content-Type: text/text');

///
/// Application Parameters
///
$config = array();

$config['key'] = getenv('API_KEY');
$config['save'] = 'files/';
$config['host'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
$config['allowed'] = array('png', 'jpg', 'gif', 'rar', 'zip', 'mp4', 'mp3', 'txt');
$config['max_upload_size'] = 25; // IN MB

///
/// Upload File
///
function UploadFile($config)
{

    // Validate Key
    if (!isset($_POST['key']) || $_POST['key'] != $config['key']) {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
        die();
    }

    // Validate ShareX
    if (!isset($_FILES['fdata'])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        die('Invalid file data!');
    }

    if ($_FILES['fdata']['size'] > $config['max_upload_size'] * 1024 * 1024) {
        header($_SERVER["SERVER_PROTOCOL"] . " 413 Payload Too Large");
        die('Too large file sent!');
    }

    // Create Data for file
    $data = array();
    $data['filename'] = $_FILES['fdata']['name'];
    $data['buffer'] = $_FILES['fdata']['tmp_name'];
    $data['extension'] = pathinfo($_FILES['fdata']['name'], PATHINFO_EXTENSION);
    $data['final-save-name'] = $config['save'] . $data['filename'] . '.' . $data['extension'];

    // Validate Extension
    if (!in_array($data['extension'], $config['allowed'])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        die('Invalid extension');
    }

    if (move_uploaded_file($data['buffer'], $data['final-save-name'])) {

        $file_signed = substr(md5(time() . rand() . $data['final-save-name']), 0, 10);
        // TODO if file name exists regenerate name
        rename($data['final-save-name'], $config['save'] . $file_signed . '.' . $data['extension']);

        header($_SERVER["SERVER_PROTOCOL"] . " 201 Created");
        die($config['host'] . $config['save'] . $file_signed . '.' . $data['extension']);
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    die("File can't be uploaded");
}

UploadFile($config);

?>
