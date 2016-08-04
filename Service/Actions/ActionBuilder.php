<?php

namespace Araneum\AdminBundle\Service\Actions;

use Symfony\Component\Routing\Router;

/**
 * Class ActionBuilder
 *
 * @package Araneum\AdminBundle\Service\Actions
 */
class ActionBuilder implements ActionBuilderInterface
{
    /** @var array */
    private $list = [];

    /** @var Router */
    private $router;

    /**
     * ActionBuilder constructor
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function add($groupName, array $actionDescription = [])
    {
        if (!array_key_exists('position', $actionDescription)) {
            throw new \InvalidArgumentException('Position must be specified');
        } elseif (!in_array($actionDescription['position'], self::POSITIONS)) {
            throw new \InvalidArgumentException(
                'Invalid position "'.$actionDescription['position'].'" must be one of '.implode(', ', self::POSITIONS)
            );
        }
        $actionPosition = $actionDescription['position'];
        $actionItem = $this->getPreparedItem($actionDescription);

        if ($actionPosition === self::POSITION_ALL) {
            $this->list[self::POSITION_ROW][$groupName][] = $actionItem;
            $this->list[self::POSITION_TOP][$groupName][] = $actionItem;
        } else {
            $this->list[$actionPosition][$groupName][] = $actionItem;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->list;
    }

    /**
     * Get prepared action item
     * generate url for resource
     *
     * @param  array $actionDescription
     * @return array
     */
    private function getPreparedItem(array $actionDescription)
    {
        unset($actionDescription['position']);

        foreach (['resource', 'resourceAll', 'form'] as $actionType) {
            if (array_key_exists($actionType, $actionDescription)) {
                $actionDescription[$actionType] = $this->router->generate($actionDescription[$actionType]);
            }
        }

        return $actionDescription;
    }
}
