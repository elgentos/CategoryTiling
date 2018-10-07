<?php

namespace Elgentos\CategoryTiling\Plugin\Model\Category\Attribute\Source;

use Magento\Catalog\Model\Category\Attribute\Source\Mode;

class ModePlugin {

    const DM_TILING = 'TILING';

    const DM_TILING_AND_PAGE = 'TILING_AND_PAGE';

    const DM_TILING_AND_PRODUCTS = 'TILING_AND_PRODUCTS';

    const DM_TILING_AND_PAGE_AND_PRODUCTS = 'TILING_AND_PAGE_AND_PRODUCTS';

    /**
     * @param Mode $subject
     * @param array $result
     * @return array
     */
    public function afterGetAllOptions(Mode $subject, array $result) : array
    {
        $tilingOptions = [
            ['value' => self::DM_TILING, 'label' => __('Tiles only')],
            ['value' => self::DM_TILING_AND_PAGE, 'label' => __('Static block and tiles')],
            ['value' => self::DM_TILING_AND_PRODUCTS, 'label' => __('Tiles and products')],
            ['value' => self::DM_TILING_AND_PAGE_AND_PRODUCTS, 'label' => __('Static block products and tiles and products')],
        ];

        return array_merge($result, $tilingOptions);
    }
}