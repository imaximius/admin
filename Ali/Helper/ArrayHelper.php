<?php

namespace Araneum\AdminBundle\Ali\Helper;

/**
 * Class ArrayHelper
 *
 * @package Araneum\AdminBundle\Ali\Helper
 */
class ArrayHelper
{

    /**
     * Searches the array for a given value and returns array corresponding values
     *
     * @param  string $needle
     * @param  array  $haystack
     * @return null|array
     */
    public static function searchLike($needle, array $haystack)
    {
        $result = null;
        foreach ($haystack as $key => $item) {
            if (stripos($item, $needle) !== false) {
                $result[$key] = $item;
            }
        }

        return $result;
    }
}
