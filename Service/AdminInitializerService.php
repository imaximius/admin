<?php

namespace Araneum\AdminBundle\Service;

use Araneum\AdminBundle\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\AdminBundle\Ali\DatatableBundle\Util\Factory\DatatableFactory;
use Araneum\AdminBundle\Service\Actions\AbstractActions;
use Araneum\AdminBundle\Service\Actions\ActionFactory;
use Symfony\Component\Form\FormTypeInterface;
use Araneum\AdminBundle\Ali\DatatableBundle\Builder\ListBuilder;

/**
 * Class AdminInitializerService
 *
 * @package Araneum\AdminBundle\Service
 */
class AdminInitializerService
{
    /** @var DatatableFactory */
    private $datatableFactory;

    /** @var FromExporterService */
    private $formExporter;

    /** @var ActionFactory */
    private $actionFactory;

    /** @var mixed */
    private $filter;

    /** @var array */
    private $action;

    /** @var array */
    private $grid;

    /** @var array */
    private $error = [];

    /**
     * AdminInitializerService constructor.
     *
     * @param FromExporterService $formExporter
     * @param DatatableFactory    $datatableFactory
     * @param ActionFactory       $actionFactory
     */
    public function __construct($formExporter, $datatableFactory, $actionFactory)
    {
        $this->formExporter = $formExporter;
        $this->datatableFactory = $datatableFactory;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Return initial array
     *
     * @return mixed
     */
    public function get()
    {
        $result = [
            'filter' => $this->filter,
            'action' => $this->action,
            'grid' => $this->grid,
        ];

        if (count($this->error)) {
            $result['errors'] = $this->error;
        }

        return $result;
    }

    /**
     * Set filters
     *
     * @param  FormTypeInterface $filter
     * @return mixed
     */
    public function setFilters($filter)
    {
        $this->filter = $this->formExporter->get($filter);

        return $this;
    }

    /**
     * Set actions
     *
     * @param  AbstractActions $action
     * @return mixed
     */
    public function setActions(AbstractActions $action)
    {
        $this->action = $this->actionFactory->create($action);

        return $this;
    }

    /**
     * Set grid
     *
     * @param  AbstractList $gridType
     * @param  string       $source
     * @return $this
     */
    public function setGrid(AbstractList $gridType, $source)
    {
        $this->grid = [
            'columns' => $this->getColumnsFromAbstractList($gridType),
            'source' => $source,
        ];

        return $this;
    }

    /**
     * Set error
     *
     * @param \Exception $error
     */
    public function setError(\Exception $error)
    {
        $this->error[] = $error->getMessage();
    }

    /**
     * Gets columns for datatable grid. If label is empty, gets name of column as property
     *
     * @param AbstractList $gridType
     * @return array
     */
    private function getColumnsFromAbstractList(AbstractList $gridType)
    {
        $builderList = new ListBuilder();
        $gridType->buildList($builderList);
        $listField = $builderList->getList();
        $columns = [];

        if (array_key_exists('_identifier_', $listField)) {
            unset($listField['_identifier_']);
        }

        foreach ($listField as $key => $item) {
            array_push(
                $columns,
                empty($item['label']) ? ucfirst($key) : $item['label']
            );
        }

        return $columns;
    }
}
