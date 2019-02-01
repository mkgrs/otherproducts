<?php

namespace Netzexpert\Otherproducts\Block\Catalog\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\Exception\NoSuchEntityException;
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
        return $this->model->getProductCollection($this->getProduct());
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
}
