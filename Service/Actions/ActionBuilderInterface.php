<?php

namespace Araneum\AdminBundle\Service\Actions;

/**
 * Interface ActionBuilderInterface
 *
 * @package Araneum\AdminBundle\Service\Actions
 */
interface ActionBuilderInterface
{
    const POSITION_TOP = 'top';
    const POSITION_ROW = 'row';
    const POSITION_ALL = 'all';
    const POSITIONS    = [
        self::POSITION_TOP,
        self::POSITION_ROW,
        self::POSITION_ALL,
    ];

    /**
     * Add new field to datatable
     *
     * @param  string $groupGroupName
     * @param  array  $actionDescription
     * @return $this
     */
    public function add($groupGroupName, array $actionDescription = []);

    /**
     * Get configured actions
     *
     * @return array
     */
    public function getActions();
}
