<?php

namespace Elgentos\CategoryTiling\Block\Category;

use Elgentos\CategoryTiling\Plugin\Model\Category\Attribute\Source\ModePlugin;
use Magento\Catalog\Model\Category;

class View extends \Magento\Catalog\Block\Category\View
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    private $outputHelper;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
        $this->outputHelper = $outputHelper;
    }

    /**
     * @return bool
     */
    public function showProducts() : bool
    {
        if ($this->getCurrentCategory()->getDisplayMode() === null) {
            return true;
        }

        return in_array($this->getCurrentCategory()->getDisplayMode(),
            [
                Category::DM_PRODUCT,
                Category::DM_MIXED,
                ModePlugin::DM_TILING_AND_PRODUCTS,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    /**
     * @return bool
     */
    public function showCmsBlock() : bool
    {
        return in_array($this->getCurrentCategory()->getDisplayMode(),
            [
                Category::DM_PAGE,
                Category::DM_MIXED,
                ModePlugin::DM_TILING_AND_PAGE,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    /**
     * @return bool
     */
    public function showTiling() : bool
    {
        return in_array($this->getCurrentCategory()->getDisplayMode(),
            [
                ModePlugin::DM_TILING,
                ModePlugin::DM_TILING_AND_PAGE,
                ModePlugin::DM_TILING_AND_PRODUCTS,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    public function getOutputHelper()
    {
        return $this->outputHelper;
    }

    /**
     * @return Category[]|\Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubcategories()
    {
        $category = $this->getCurrentCategory();
        $childrenCategories = $category->getChildrenCategories();

        if ($childrenCategories instanceof \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection) {
            $childrenCategories->addAttributeToSelect('image')
                ->addAttributeToSelect('image_url')
                ->addAttributeToSelect('url');
        }

        return $childrenCategories;
    }
}
