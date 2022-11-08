<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\CategoryTiling\Block\Category;

use Elgentos\CategoryTiling\Model\Config;
use Elgentos\CategoryTiling\Plugin\Model\Category\Attribute\Source\ModePlugin;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class View extends \Magento\Catalog\Block\Category\View
{
    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    private Config $config;

    /**
     * View constructor.
     *
     * @param Context           $context
     * @param Resolver          $layerResolver
     * @param Registry          $registry
     * @param CategoryHelper    $categoryHelper
     * @param Output            $outputHelper
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        CategoryHelper $categoryHelper,
        Output $outputHelper,
        CollectionFactory $collectionFactory,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
        $this->config            = $config;
        $this->outputHelper      = $outputHelper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Check if the product should be shown.
     *
     * @return bool
     */
    public function showProducts(): bool
    {
        if ($this->getCurrentCategory()->getDisplayMode() === null) {
            return true;
        }

        return in_array(
            $this->getCurrentCategory()->getDisplayMode(),
            [
                Category::DM_PRODUCT,
                Category::DM_MIXED,
                ModePlugin::DM_TILING_AND_PRODUCTS,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    /**
     * Check if a CMS block should be displayed.
     *
     * @return bool
     */
    public function showCmsBlock(): bool
    {
        if ($this->config->showTilingOnFilteredCategoryPage() === false
            && $this->isFilteredRequest()
        ) {
            return false;
        }

        return in_array(
            $this->getCurrentCategory()->getDisplayMode(),
            [
                Category::DM_PAGE,
                Category::DM_MIXED,
                ModePlugin::DM_TILING_AND_PAGE,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    /**
     * Whether query parameters are filled in for this request.
     *
     * @return bool
     */
    private function isFilteredRequest(): bool
    {
        $request = $this->getRequest();
        return $request instanceof Request && $request->getQuery()->count() > 0;
    }

    /**
     * Check if the category tiling should be shown.
     *
     * @return bool
     */
    public function showTiling(): bool
    {
        if ($this->config->showTilingOnFilteredCategoryPage() === false
            && $this->isFilteredRequest()
        ) {
            return false;
        }
        
        return in_array(
            $this->getCurrentCategory()->getDisplayMode(),
            [
                ModePlugin::DM_TILING,
                ModePlugin::DM_TILING_AND_PAGE,
                ModePlugin::DM_TILING_AND_PRODUCTS,
                ModePlugin::DM_TILING_AND_PAGE_AND_PRODUCTS
            ]
        );
    }

    /**
     * Return the catalog output helper
     *
     * @return Output
     */
    public function getOutputHelper(): Output
    {
        return $this->outputHelper;
    }

    /**
     * Get the subcategories of the current category
     *
     * @return Collection|null
     * @throws LocalizedException
     */
    public function getSubcategories(): ?Collection
    {
        $category              = $this->getCurrentCategory();
        $childrenCategoryIds   = explode(',', $category->getChildren());
        $additionalCategoryIds = $category->getData('additional_categories')
            ? array_map('trim', explode(',', $category->getData('additional_categories')))
            : [];
        $allCategoryIds        = array_merge($childrenCategoryIds, $additionalCategoryIds);

        if (!count($allCategoryIds)) {
            return null;
        }

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect(['name', 'image', 'image_url', 'url'])
            ->addAttributeToFilter('entity_id', ['in' => $allCategoryIds])
            ->setOrder('position','ASC');

        return $collection;
    }
}
