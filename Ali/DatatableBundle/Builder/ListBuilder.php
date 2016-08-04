<?php

namespace Araneum\AdminBundle\Ali\DatatableBundle\Builder;

use Closure;

/**
 * Class ListBuilder
 *
 * @package Araneum\AdminBundle\Ali\DatatableBundle\Builder
 */
class ListBuilder implements ListBuilderInterface
{
    /** @var array */
    private $list = [];

    /** @var bool */
    private $search     = true;

    /** @var Closure */
    private $widgetData = null;

    /** @var array */
    private $orderBy    = [];

    /**
     * {@inheritdoc}
     */
    public function add($name, array $fieldDescriptionOptions = [])
    {
        $fieldDescriptionOptions['column'] = count($this->list);
        $this->list[$name] = $fieldDescriptionOptions;

        return $this;
    }

    /**
     * Set search enabled
     *
     * @param  bool $search
     * @return $this
     */
    public function setSearch($search = true)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Add widget to datatable
     *
     * @param  Closure $data
     * @return $this
     * @throws \Exception
     */
    public function setWidget(\Closure $data)
    {
        if (!empty($this->widgetData)) {
            throw new \Exception('Only one widget in datatable');
        }

        $this->widgetData = $data;

        return $this;
    }

    /**
     * Set field for order
     *
     * @param  string $field
     * @param  string $sort
     * @return mixed
     */
    public function setOrderBy($field, $sort = 'DESC')
    {
        $this->orderBy = [
            'field' => $field,
            'sort' => $sort,
        ];

        return $this;
    }

    /**
     * Get OrderBy
     *
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Return true if search is enabled
     *
     * @return bool
     */
    public function isSearchEnabled()
    {
        return $this->search;
    }

    /**
     * Get List
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Get WidgetData
     *
     * @return closure
     */
    public function getWidgetData()
    {
        return $this->widgetData;
    }
}
