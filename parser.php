<?php
class parser
{
    function parsePeppolXML($filename)
    {
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
        echo "invoice id: " . $invoiceId . "</br>";

        $invoiceDate = $header->IssueDate;
        echo "invoice date: " . $invoiceDate . "</br>";

        $cac = $xml->children($namespaces['cac']);
        $body = $cac->AccountingCustomerParty->children($namespaces['cac']);
        $customerParty = $body->Party->PartyName->children($namespaces['cbc']);
        $customerName = $customerParty->Name;

        echo "customer name: " . $customerName . "</br>";

        $customerLegalEntity = $body->Party->PartyLegalEntity->children($namespaces['cbc']);
        $customerVat = $customerLegalEntity->CompanyID;
        echo "customer VAT: " . $customerVat . "</br>";

        $taxTotal = $cac->TaxTotal->children($namespaces['cac'])->TaxSubtotal->children($namespaces['cbc']);
        $taxExcl = $taxTotal->TaxableAmount;
        $taxAmount = $taxTotal->TaxAmount;

        $taxCat = $cac->TaxTotal->children($namespaces['cac'])->TaxSubtotal->children($namespaces['cac'])->TaxCategory;
        $taxPercentage = $taxCat->children($namespaces['cbc'])->Percent;
        echo "percentage: " . $taxPercentage . "</br>";
        echo "amount without tax: " . $taxExcl . "</br>";
        echo "tax amount: " . $taxAmount . "</br>";
        echo "<hr>";

        $parsedData = array(
            "id" => (string)$invoiceId,
            "date" => (string)$invoiceDate,
            "name" =>  (string)$customerName,
            "VAT" =>  (string)$customerVat,
            "percentage" => (double)$taxPercentage,
            "taxExcl" => (double)$taxExcl,
            "taxAmount" => (double)$taxAmount
        );
        return $parsedData;
    }
}