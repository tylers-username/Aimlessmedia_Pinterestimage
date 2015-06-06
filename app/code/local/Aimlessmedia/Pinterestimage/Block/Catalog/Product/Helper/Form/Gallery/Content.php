<?
class Aimlessmedia_Pinterestimage_Block_Catalog_Product_Helper_Form_Gallery_Content extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content
{
    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        $generatePinImageUrl=Mage::getModel('adminhtml/url')->getUrl('*/pinimage/generateimage', array(
            '_current'   => true
        ));
        $buttonText=$this->__("Generate Pinterest Image");
        $promptText=$this->__("Warning!\\r\\nIf you have made any changes to this product you will need to save those changes.\\r\\nAre you sure you want to continue?");
        $jsConfirmationPrompt='if ( confirm("'.$promptText.'") ){ window.location.href = "'.$generatePinImageUrl.'";}';
        return $this->getChildHtml('uploader').'<button id="'.md5(rand(1,100)).'" title="'.$buttonText.'" type="button" class="pinGenButton" style="float:right" onclick=\''.$jsConfirmationPrompt.'\' style=""><span><span><span>'.$buttonText.'</span></span></span></button>';
    }
}