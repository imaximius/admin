<?php

namespace Araneum\AdminBundle\Service\Actions;

/**
 * Class AbstractActions
 *
 * @package Araneum\AdminBundle\Service\Actions
 */
abstract class AbstractActions
{
    /**
     * Build the list
     *
     * @param ActionBuilderInterface $builder
     */
    abstract public function buildActions(ActionBuilderInterface $builder);
}
