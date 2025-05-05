<?php
session_start();
if (isset($_SESSION['parsedData'])) {
    $parsedDataArray = $_SESSION['parsedData'];
    // Set headers to force CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="parsed_data.csv"');

    // Open PHP output stream
    $output = fopen('php://output', 'w');

    fputcsv($output, ['id', 'date', 'name', 'VAT', 'tax percentage', 'total excl tax', 'total tax']);

    foreach ($parsedDataArray as $data) {
        fputcsv($output, $data);
    }

    fclose($output);
    exit;
} else {
    echo "No valid data to export.";
}