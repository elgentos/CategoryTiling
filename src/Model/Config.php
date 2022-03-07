<?php

declare(strict_types=1);

namespace Elgentos\CategoryTiling\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

final class Config
{
    private const CONFIG_SHOW_TILING_ON_FILTERED_CATEGORY_PAGE = 'elgentos_categorytiling/category/show_tiling_filtered';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function showTilingOnFilteredCategoryPage(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_SHOW_TILING_ON_FILTERED_CATEGORY_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
