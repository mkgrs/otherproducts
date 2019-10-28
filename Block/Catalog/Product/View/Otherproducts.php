<?php

namespace Netzexpert\Otherproducts\Block\Catalog\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Otherproducts extends Template
{
    /** @var \Netzexpert\Otherproducts\Model\Product\Otherproducts  */
    private $model;
    /** @var Registry  */
    private $registry;
    /** @var ProductRepositoryInterface  */
    private $productRepository;

    private $imageFactory;

    /**
     * Otherproducts constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param ProductRepositoryInterface $repository
     * @param \Netzexpert\Otherproducts\Model\Product\Otherproducts $model
     * @param ImageFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductRepositoryInterface $repository,
        \Netzexpert\Otherproducts\Model\Product\Otherproducts $model,
        ImageFactory $imageFactory,
        array $data = []
    ) {
        $this->model                = $model;
        $this->registry             = $registry;
        $this->productRepository    = $repository;
        $this->imageFactory         = $imageFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws NoSuchEntityException
     */
    public function getProductCollection()
    {
        $catIds = ($this->getData('catIds')) ? $this->getData('catIds') : $this->getProduct()->getCategoryIds();
        return $this->model->getProductCollection($this->getProduct()->getId(), $catIds);
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     * @throws NoSuchEntityException
     */
    public function getProduct()
    {
        if (!$this->registry->registry('product') && $this->getData('product_id')) {
            $product = $this->productRepository->getById($this->getData('product_id'));
            $this->registry->register('product', $product);
        }
        return $this->registry->registry('product');
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageFactory->create($product, $imageId, $attributes);
    }

    /**
     * Get product price.
     *
     * @param Product $product
     * @return string
     */
    public function getProductPrice(Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products.
     * (rendering happens in the scope of product list, but not single product)
     *
     * @return Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        if ($this->getChildBlock('toolbar')) {
            return $this->getChildBlock('toolbar')->getCurrentMode();
        }

        return $this->getDefaultListingMode();
    }

    /**
     * Get listing mode for products if toolbar is removed from layout.
     * Use the general configuration for product list mode from config path catalog/frontend/list_mode as default value
     * or mode data from block declaration from layout.
     *
     * @return string
     */
    private function getDefaultListingMode()
    {
        // default Toolbar when the toolbar layout is not used
        $defaultToolbar = $this->getToolbarBlock();
        $availableModes = $defaultToolbar->getModes();

        // layout config mode
        $mode = $this->getData('mode');

        if (!$mode || !isset($availableModes[$mode])) {
            // default config mode
            $mode = $defaultToolbar->getCurrentMode();
        }

        return $mode;
    }

    /**
     * Retrieve child block by name
     *
     * @param string $alias
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     */
    public function getChildBlock($alias)
    {
        $layout = $this->getLayout();
        if (!$layout) {
            return false;
        }
        $name = $layout->getChildName($this->getNameInLayout(), $alias);
        if ($name) {
            return $layout->getBlock($name);
        }
        return false;
    }
}