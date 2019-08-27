<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 27.08.2019
 * Time: 12:50
 */

namespace Netzexpert\Otherproducts\Test\Block\Catalog\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Netzexpert\Otherproducts\Block\Catalog\Product\View\Otherproducts;
use Netzexpert\Otherproducts\Model\Product\Otherproducts as OtherproductsModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OtherproductsTest extends TestCase
{

    /**
     * @var MockObject
     */
    private $contextMock;
    /**
     * @var MockObject
     */
    private $registryMock;
    /**
     * @var MockObject
     */
    private $productRepositoryMock;
    /**
     * @var MockObject
     */
    private $otherProductsModelMock;
    /**
     * @var MockObject
     */
    private $imageFactoryMock;
    /**
     * @var MockObject
     */
    private $productMock;
    /**
     * @var MockObject
     */
    private $imageMock;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Otherproducts | MockObject
     */
    private $block;
    /**
     * @var MockObject
     */
    private $collectionMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->createMock(Context::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->otherProductsModelMock = $this->createMock(OtherproductsModel::class);
        $this->imageFactoryMock = $this->createMock(ImageFactory::class);
        $this->productMock = $this->createMock(Product::class);
        $this->imageMock = $this->createMock(Image::class);
        $this->collectionMock = $this->createMock(Collection::class);
        $this->block = $this->objectManager->getObject(
            Otherproducts::class,
            [
                'model' => $this->otherProductsModelMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'productRepository' => $this->productRepositoryMock,
                'imageFactory' => $this->imageFactoryMock
            ]
        );
    }

    public function testGetProduct()
    {
        $this->registryMock->expects($this->any())
        ->method('registry')
        ->with('product')
        ->willReturn($this->productMock);
        $this->assertEquals($this->productMock, $this->block->getProduct());
    }

    public function testGetProductNoProduct()
    {
        $this->registryMock->expects($this->at(0))
            ->method('registry')
            ->with('product')
            ->willReturn(null);
        $this->block->setData('product_id', 1);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($this->productMock);
        $this->registryMock->expects($this->at(1))
            ->method('register')
            ->with('product');
        $this->registryMock->expects($this->at(2))
            ->method('registry')
            ->with('product')
            ->willReturn($this->productMock);
        $this->assertEquals($this->productMock, $this->block->getProduct());
    }

    public function testGetProductNoData()
    {
        $this->registryMock->expects($this->at(0))
            ->method('registry')
            ->with('product')
            ->willReturn(null);
        $this->block->setData('product_id', null);
        $this->registryMock->expects($this->at(1))
            ->method('registry')
            ->with('product')
            ->willReturn(null);
        $this->assertNull($this->block->getProduct());
    }

    public function testGetImage()
    {
        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->productMock, 'image', [])
            ->willReturn($this->imageMock);
        $this->assertEquals($this->imageMock, $this->block->getImage($this->productMock, 'image', []));
    }

    public function testGetProductCollection()
    {
        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->willReturn($this->productMock);
        $this->otherProductsModelMock->expects($this->once())
            ->method('getProductCollection')
            ->with($this->productMock)
            ->willReturn($this->collectionMock);
        $this->assertEquals($this->collectionMock, $this->block->getProductCollection());
    }
}
