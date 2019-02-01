<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 22/01/19
 * Time: 16:34
 */

namespace Netzexpert\Otherproducts\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Otherproducts
{
    const XML_PATH_OTHERPRODUCTS = 'otherproducts/general';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    /**
     * Otherproducts constructor.
     * @param CollectionFactory $collectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $product Product
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection($product)
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
        $categoryIds = $product->getCategoryIds();
        $productCollection->addAttributeToSelect('*')
            ->addCategoriesFilter(['in' => implode(',', $categoryIds)])
            ->addFieldToFilter('entity_id', ['neq'=>$product->getId()]);
        if ($limit) {
            $productCollection->setPageSize($limit)
            ->setCurPage(1);
        }
        return $productCollection;
    }
}
