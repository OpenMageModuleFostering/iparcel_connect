<?php
/**
 * i-parcel custom post script block
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author       Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Html_Head_Post extends Mage_Core_Block_Template
{
    /**
     * Checking if there's store config for custom post script, if yes returning script directory, if not returning default directory
     *
     * @return string
     */
    public function getScript()
    {
        $file = Mage::getStoreConfig('iparcel/scripts/post');
        if ($file) {
            return Mage::getModel('shippingip/config_script_js')->getUploadDir('post').$file;
        } else {
            return 'iparcel/post.js';
        }
    }
}
