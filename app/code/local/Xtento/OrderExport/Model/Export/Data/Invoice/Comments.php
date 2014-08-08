<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2013-03-30T18:53:22+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Invoice/Comments.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Invoice_Comments extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Invoice Comments',
            'category' => 'Invoice',
            'description' => 'Export any comments added to invoices, retrieved from the sales_flat_invoice_comment table.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_INVOICE),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['invoice_comments'];
        // Fetch fields to export
        $invoice = $collectionItem->getInvoice();

        if (!$this->fieldLoadingRequired('invoice_comments')) {
            return $returnArray;
        }

        if ($invoice) {
            $commentsCollection = $invoice->getCommentsCollection();
            if ($commentsCollection) {
                foreach ($commentsCollection->getItems() as $invoiceComment) {
                    $this->_writeArray = & $returnArray['invoice_comments'][];
                    $this->writeValue('comment', $invoiceComment->getComment());
                    $this->writeValue('created_at', $invoiceComment->getCreatedAt());
                }
            }
        }
        $this->_writeArray = & $returnArray;
        // Done
        return $returnArray;
    }
}