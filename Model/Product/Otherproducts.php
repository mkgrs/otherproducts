<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 22/01/19
 * Time: 16:34
 */

namespace Netzexpert\Otherproducts\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Otherproducts
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /** @var ScopeConfigInterface  */
    private $scopeConfig;
    /**
     * @var Stock
     */
    private $stockFilter;

    /**
     * Otherproducts constructor.
     * @param CollectionFactory $collectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        Stock $stockFilter
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->stockFilter = $stockFilter;
    }

    /**
     * @param $productId int
     * @param $categoryIds array
     * @return Collection
     */
    public function getProductCollection($productId, $categoryIds)
    {
        $enabled = $this->scopeConfig->getValue(
            'otherproducts/general/enable',
            ScopeInterface::SCOPE_STORE
        );
        $limit = (int)$this->scopeConfig->getValue(
            'otherproducts/general/products_amount',
            ScopeInterface::SCOPE_STORE
        );
        if (!$enabled) {
            return null;
        }
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToSelect('*')
            ->addCategoriesFilter(['in' => implode(',', $categoryIds)])
            ->addFieldToFilter('entity_id', ['neq'=>$productId])
            ->addFieldToFilter('status', ['eq'=>'1']);
        $this->stockFilter->addInStockFilterToCollection($productCollection);
        if ($limit) {
            $productCollection->setPageSize($limit)
            ->setCurPage(1);
        }
        return $productCollection;
    }
}
