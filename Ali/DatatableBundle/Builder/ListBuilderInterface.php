<?php

namespace Araneum\AdminBundle\Ali\DatatableBundle\Builder;

/**
 * Interface ListBuilderInterface
 *
 * @package Araneum\AdminBundle\Ali\DatatableBundle\Builder
 */
interface ListBuilderInterface
{
    /**
     * Add new field to datatable
     *
     * @param  string $name
     * @param  array  $fieldDescriptionOptions
     * @return $this
     */
    public function add($name, array $fieldDescriptionOptions = []);

    /**
     * Set search enabled
     *
     * @param  bool $search
     * @return $this
     */
    public function setSearch($search = true);

    /**
     * Add widget to datatable
     *
     * @param  \Closure $data
     * @return $this
     */
    public function setWidget(\Closure $data);

    /**
     * Set field for order
     *
     * @param  string $field
     * @param  string $sort
     * @return $this
     */
    public function setOrderBy($field, $sort = 'desc');
}
