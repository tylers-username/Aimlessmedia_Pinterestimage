<?php

/**
 * Created by PhpStorm.
 * User: tmills
 * Date: 6/4/2015
 * Time: 3:05 PM
 */
class Aimlessmedia_Pinterestimage_Adminhtml_PinimageController extends Mage_Adminhtml_Controller_Action
{
    function generateImageAction()
    {

        if (!is_numeric($prodID = $this->getRequest()->getParam('id'))) {
            Mage::getSingleton("core/session")->addNotice("A Pinterest image could not be generated for the following IDs because they are not numeric: $prodID");
        }
        $generateImage=Mage::getModel('aimlessmedia_pinterestimage/adminhtml_catalog_product_pinimage')->generatePinterestImage($prodID);
        if ($generateImage) {
            Mage::getSingleton("core/session")->addSuccess($this->__("Your Pinterest image has been generated and set to Default Pinterest Image. It is excluded from the frontend gallery view by default."));
            Mage::getSingleton("core/session")->addSuccess($this->__("The exclude option does not impact Pinterest pulling the image."));
            Mage::getSingleton("core/session")->addSuccess($this->__("View the generated Pinterest image by browsing to your product images."));
        }
        $returnToProduct = Mage::getModel('adminhtml/url')->getUrl('*/catalog_product/edit', array(
            '_current' => true
        ));
        header("Location: $returnToProduct");
        exit;
    }

    function massGenerateImageAction()
    {
        $productToMod = $this->getRequest()->getParam("product");
        $failedIDs = [];
        foreach ($productToMod as $prodID) {
            if (!is_numeric($prodID)) {
                $failedIDs[] = $prodID;
                continue;
            }
            Mage::getModel('aimlessmedia_pinterestimage/adminhtml_catalog_product_pinimage')->generatePinterestImage($prodID);
        }
        if (isset($failedIDs[1])) {
            Mage::getSingleton("core/session")->addNotice("A Pinterest image could not be generated for the following IDs because they are not numeric: " . implode(", ", $failedIDs));
        }
        $url = Mage::getModel('adminhtml/url')->getUrl('*/catalog_product', array(
            '_current' => true
        ));
        $this->_redirectUrl($url);


    }
}