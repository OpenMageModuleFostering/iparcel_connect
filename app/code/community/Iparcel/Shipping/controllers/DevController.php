<?php
/**
 * External Ajax request controller
 *
 * @category        Iparcel
 * @package             Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_DevController extends Mage_Core_Controller_Front_Action
{
    /**
     * Preparing headers for external ajax
     */
    protected function _prepareHeaders()
    {
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Access-Control-Allow-Origin', '*');
    }

    /**
     * Responding with region list for uri-specified country
     */
    public function regionAction()
    {
        $this->_prepareHeaders();
        $countryId = $this->getRequest()->getParam('country_id');
        $_country = Mage::getModel('directory/country')->loadByCode($countryId);
        /* var $_country Mage_Directory_Model_Country */
        $iso3 = $_country->getIso3Code();
        $_regionCollection = Mage::getModel('directory/region')
            ->getCollection()
            ->addCountryCodeFilter($iso3);
        /* var $_regionCollection Mage_Directory_Model_Resource_Region_Collection */
        $response = array();
        foreach ($_regionCollection as $_region) {
            $response[$_region->getCode()] = $_region->getDefaultName();
        }
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json');
        echo json_encode($response);
    }

    /**
     * Responding with recent orders of uri-specified user
     */
    public function recentOrdersAction()
    {
        $this->_prepareHeaders();
        $email = $this->getRequest()->getParam('email');
        $count = $this->getRequest()->getParam('count');
        try {
            if (!$email) {
                Mage::throwException('User e-mail address is not specified');
            }
            if (!$count) {
                Mage::throwException('Order count is not specified');
            }
            $customerId = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email)
                ->getId();
            if (!$customerId) {
                Mage::throwException('User with specified e-mail does not exist');
            }
            $_orderCollection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('entity_id', 'DESC')
                ->setPageSize($count)
                ->setCurPage(1);
            /* var $_orderCollection Mage_Sales_Model_Resource_Order_Collection */
            $response = array();
            foreach ($_orderCollection as $_order) {
                $response[] = array(
                    'increment_id' => $_order->getIncrementId(),
                    'created_at' => $_order->getCreatedAt()
                );
            }
            echo json_encode($response);
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }
    }

    public function orderAction()
    {
        $requestString = 'user[email]=tester@i-parcel.com&user[firstname]=Stefan&user[lastname]=eCom-Test&user[password]=nopassword&user[address][new]=1&user[address][firstname]=Stefan&user[address][lastname]=eCom-Test&user[address][street]=25 Test St &user[address][city]=York&user[address][region_id]=PA&user[address][postcode]=555555&user[address][country_id]=GB&user[address][telephone]=555-555-5555&key=63288480-915F-4DBD-9913-AD710742AC9E&tax=78.36&shipping=24.72&currency=USD&tracking[email]=tester@i-parcel.com&tracking[number]=1205535867US&orders[0][sku]=hdb008&orders[0][qty]=1&orders[0][price]=240.00';

        parse_str($requestString, $data);

        $this->_forward('add', 'order', 'shippingip', $data);
    }
}
