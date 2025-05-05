<?php
require_once('parser.php');
session_start();
$parsedDataArray = [];
// took me a while to figure out without AI, dont have time to add csv and pdf now, might do later
if (isset($_FILES['uploadfile'])) {
    $showButton = false;
    $peppolParser = new parser();
    for ($i = 0; $i < count($_FILES['uploadfile']['name']); $i++) {
        if ($_FILES['uploadfile']['error'][$i] === UPLOAD_ERR_OK) {
            $filename = $_FILES['uploadfile']['tmp_name'][$i];
            $parsedData = $peppolParser->parsePeppolXML($filename);
            $parsedDataArray[] = $parsedData;
        }
    }
    if (!empty($parsedDataArray)) {
        $_SESSION['parsedData'] = $parsedDataArray;
        $showButton = true;
    } else {
        echo "something when wrong parsing data.";
    }



} else {
    echo 'No file uploaded or upload error.';
} ?>

<?php if ($showButton): ?>
    <a href="csv_download.php"> download CSV</a>
<?php endif; ?>