<?
echo 'Running This Upgrade: in mysql4 file '.get_class($this)."\n <br /> \n";
die("Exit for now");
$installer = $this;
$installer->startSetup();
$this->addAttribute(
    'catalog_product',
    'pinterest_image',
    array (
        'group'             => 'Images',
        'type'              => 'varchar',
        'frontend'          => 'catalog/product_attribute_frontend_image',
        'label'             => 'Default Pinterest Image',
        'input'             => 'media_image',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
    )
);
$installer->endSetup();