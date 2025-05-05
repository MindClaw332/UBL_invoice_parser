<?php
// took me a while to figure out without AI, dont have time to add csv and pdf now, might do later
if (isset($_FILES['uploadfile'])) {
    for ($i = 0; $i < count($_FILES['uploadfile']['name']); $i++) {
        if ($_FILES['uploadfile']['error'][$i] === UPLOAD_ERR_OK) {
            $filename = $_FILES['uploadfile']['tmp_name'][$i];
            $xmlString = file_get_contents($filename);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlString);
            if ($xml === false) {
                echo "Failed to parse XML.<br>";
                foreach (libxml_get_errors() as $error) {
                    echo htmlspecialchars($error->message) . "<br>";
                }
                libxml_clear_errors();
                exit;
            }

            $namespaces = $xml->getNamespaces(true);

            $header = $xml->children($namespaces['cbc']);
            $invoiceId = $header->ID;
            echo "invoice id: ".$invoiceId . "</br>";

            $invoiceDate = $header->IssueDate;
            echo "invoice date: ".$invoiceDate . "</br>";

            $cac = $xml->children($namespaces['cac']);
            $body = $cac->AccountingCustomerParty->children($namespaces['cac']);
            $customerParty = $body->Party->PartyName->children($namespaces['cbc']);
            $customerName = $customerParty->Name;

            echo "customer name: ".$customerParty . "</br>";

            $customerLegalEntity = $body->Party->PartyLegalEntity->children($namespaces['cbc']);
            $customerVat = $customerLegalEntity->CompanyID;
            echo "customer VAT: ".$customerVat . "</br>";

            $taxTotal = $cac->TaxTotal->TaxSubtotal->children($namespaces['cbc']);
            $taxExcl = $taxTotal->TaxableAmount;
            $taxAmount = $taxTotal->TaxAmount;
            echo "amount without tax: ".$taxExcl . "</br>";
            echo "tax amount: ".$taxAmount . "</br>";
            echo"<hr>";
        }
    }

} else {
    echo 'No file uploaded or upload error.';
} 
