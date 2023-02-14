<?php

namespace Magestar\CategoryBestSeller\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION = 'categorybestseller';

    /**
     * Get settings
     *
     * @return string
     */
    public function getCfg($optionString)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION . '/' . $optionString, ScopeInterface::SCOPE_STORE);
    }

}
