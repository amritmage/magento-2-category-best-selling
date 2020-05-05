<?php 

namespace Magestar\BestSellers\Block;

class BestSellers extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_collectionFactory;
    protected $_productsFactory;
    protected $_bestSellerLimit = 4;
    protected $_period = 'year';
    protected $_bestSellerProductIds = [];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        array $data = []
    ) {
  
        $this->_collectionFactory = $collectionFactory;
        $this->_registry = $registry;
        $this->_productsFactory = $productsFactory;
        parent::__construct($context, $data);
    }

    public function getCurrentCategory(){
        return $this->_registry->registry('current_category');
    }

    public function getCategoryId(){
        return $this->getCurrentCategory()->getID();
    }

    public function getCategoryName(){
        return $this->getCurrentCategory()->getName();
    }

    protected function _getBestSellerProductIds(){

        $result = [];

        
        $collection = $this->_collectionFactory->create()
                            ->setModel('Magento\Catalog\Model\Product')
                            ->setPeriod($this->_period);

        if(isset($collection)){

            if($this->getCategoryId() != 2){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $categoryId = $this->getCategoryId();

                $mainTable = $collection->getTableByAggregationPeriod($this->_period.'ly');

                $sql = "SELECT `$mainTable`.`product_id` FROM `$mainTable` ,catalog_category_product WHERE catalog_category_product.`product_id` = `$mainTable`.`product_id` AND catalog_category_product.`category_id` = '$categoryId' GROUP BY `$mainTable`.`product_id` ORDER BY SUM(`$mainTable`.`qty_ordered`) DESC LIMIT 4";
            
                $result = $connection->fetchAll($sql);
            }
        }
        
        return $result;
    }

    public function getBestSellerData(){

        $result = '';

        if($this->_getBestSellerProductIds()){
            $ids = $this->_getBestSellerProductIds();

            $result = $this->_productsFactory->create()->addFieldToFilter('entity_id', array('in', $ids));
            return $result;
        }else{
            return $result;
        }
    }

}