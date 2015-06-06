<?

class Aimlessmedia_Pinterestimage_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        // Append new mass action option
        /*        $this->getMassactionBlock()->addItem(
                    'newmodule',
                    array('label' => $this->__('New Mass Action Title'),
                        'url'   => $this->getUrl('newmodule/controller/action') //this should be the url where there will be mass operation
                    )
                ); */
        $this->getMassactionBlock()->addItem('pinterestimage', array(
                'label' => Mage::helper('catalog')->__('Generate Pin Image'),
                'url' => $this->getUrl('*/pinimage/massgenerateimage', array('_current' => true)),
                'confirm' => $this->__("-If you are updating a large number of products it is recommended that you temporarily disable Indexing.\n-This will generate new images and set them as the pinterest default.\n-It will not remove previously generate images.")
            )
        );

    }
}