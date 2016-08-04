<?php

namespace Araneum\AdminBundle\Ali\DatatableBundle\Util\Factory\Query;

use Ali\DatatableBundle\Util\Factory\Query\DoctrineBuilder;
use Araneum\AdminBundle\Ali\Helper\TimeCounterHelper;

/**
 * Class AraneumDoctrineBuilder
 *
 * @package Araneum\AdminBundle\Ali\DatatableBundle\Util\Factory\Query
 */
class AraneumDoctrineBuilder extends DoctrineBuilder
{
    const SEARCH_TIME_FORMAT = 'Y-m-d H:i:s.u';

    /** @var object */
    protected $searchQuery;

    /** @var bool */
    protected $isEmptyQuery = false;

    /**
     * Set search query
     *
     * @param object $searchQuery
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * Get search query
     *
     * @return mixed
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * Search case insensitive like
     *
     * @param  string $searchField
     * @param  string $value
     * @return string
     */
    public function searchLike($searchField, $value)
    {
        return " UPPER($searchField) LIKE UPPER('%{$value}%') ";
    }

    /**
     * Search equals
     *
     * @param  string $searchField
     * @param  string $value
     * @return string
     */
    public function searchEquals($searchField, $value)
    {
        return " $searchField = '$value' ";
    }

    /**
     * Search by date interval changed with the date format
     *
     * @param  string $searchField
     * @param  string $date
     * @return string
     */
    public function searchDateIntervalDay($searchField, $date)
    {
        try {
            if (!preg_match('/^\d{4}/', $date)) {
                return false;
            }

            $dateObject = new \DateTime($date);
            $dateObject->setTime(0, 0, 0);
        } catch (\Exception $e) {
            return false;
        }

        if (preg_match('/^\d{4}$/', $date)) {
            $dateObject->setDate($date, 1, 1);
            $dtFrom = $dateObject->modify('first day of this year')->format(self::SEARCH_TIME_FORMAT);
            $dtTo = $dateObject->modify('first day of next year')->format(self::SEARCH_TIME_FORMAT);
        } elseif (preg_match('/^\d{4}-\d{2}$/', $date)) {
            $dtFrom = $dateObject->modify('first day of this month')->format(self::SEARCH_TIME_FORMAT);
            $dtTo = $dateObject->modify('first day of next month')->format(self::SEARCH_TIME_FORMAT);
        } else {
            $dtFrom = $dateObject->format(self::SEARCH_TIME_FORMAT);
            $dtTo = $dateObject->modify('+1 day')->format(self::SEARCH_TIME_FORMAT);
        }

        return " ( $searchField > '$dtFrom' AND $searchField < '$dtTo' )  ";
    }

    /**
     * Search in array
     *
     * @param  string $searchField
     * @param  string $value
     * @return string
     */
    public function searchIn($searchField, $value)
    {
        return " $searchField IN (".implode(',', $value).') ';
    }

    /**
     * Search time
     *
     * @param  string $searchField
     * @param  string $value
     * @return string
     */
    public function searchTime($searchField, $value)
    {
        if (is_array($value)) {
            $timeInHour = TimeCounterHelper::TIME_IN_HOUR;
            $timeInMinute = TimeCounterHelper::TIME_IN_MINUTE;

            $timeArray = $value;
            $hours = (int) $timeArray[0];
            $seconds = (int) $timeArray[1];
            $minusesWithHour = (int) $timeArray[1];
            $minusesWithSecond = (int) $timeArray[0];

            $searchByHourAndMinutes = "($searchField / $timeInHour = $hours
            AND MOD($searchField , $timeInHour) / $timeInMinute = $minusesWithHour
            )";
            $searchByMinutesAndSeconds = "(
                MOD($searchField , $timeInHour) / $timeInMinute = $minusesWithSecond
                AND MOD(MOD($searchField , $timeInHour) , $timeInMinute) = $seconds
            )";

            return $searchByHourAndMinutes." OR ".$searchByMinutesAndSeconds;
        }

        return false;
    }

    /**
     * Get QueryBuilder with search query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getResultQueryBuilder()
    {
        $qb = clone $this->queryBuilder;
        $this->_addSearch($qb);

        return $qb;
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * @param  null $select
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addSelect($select = null)
    {
        return $this->queryBuilder->addSelect($select);
    }

    /**
     * Is empty query
     *
     * @return boolean
     */
    public function isEmptyQuery()
    {
        return $this->isEmptyQuery;
    }

    /**
     * Set is empty query
     *
     * @param boolean $isEmptyQuery
     */
    public function setIsEmptyQuery($isEmptyQuery)
    {
        $this->isEmptyQuery = $isEmptyQuery;
    }

    /**
     * Get data
     *
     * @param  int $hydrationMode
     * @return array
     */
    public function getData()
    {
        if ($this->isEmptyQuery()) {
            return [
                [],
                [],
            ];
        }

        return parent::getData();
    }

    /**
     * Get total count
     *
     * @return int|mixed
     */
    public function getTotalRecords()
    {
        $qb = clone $this->queryBuilder;
        $this->_addSearch($qb);

        if ($this->isEmptyQuery()) {
            return 0;
        }

        $qb->resetDQLPart('orderBy');

        $gb = $qb->getDQLPart('groupBy');
        if (empty($gb) || !in_array($this->fields['_identifier_'], $gb)) {
            $qb->select(" count({$this->fields['_identifier_']}) ");
        } else {
            $qb->resetDQLPart('groupBy');
            $qb->select(" count(distinct {$this->fields['_identifier_']}) ");
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get the search dql
     *
     * @param  \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return void
     */
    protected function _addSearch(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ($this->search == true) {
            $request = $this->request;
            $searchFields = array_keys($this->getSearchQuery());

            $searchParam = trim($request->get("sSearch"));
            if (empty($searchParam)) {
                return;
            }

            $orQueries = [];
            $searchQueryArray = $this->getSearchQuery();
            foreach ($searchFields as $searchField) {
                if (!isset($searchQueryArray[$searchField])) {
                    continue;
                }

                $field = explode(' ', trim($searchField));
                $searchField = $field[0];
                if (($oneQuery = $searchQueryArray[$searchField]($this, $searchField, $searchParam)) !== false
                    && !empty($oneQuery)
                ) {
                    $orQueries[] = $oneQuery;
                }
            }

            if (empty($orQueries)) {
                $this->setIsEmptyQuery(true);
            }

            if (!empty($orQueries)) {
                $orQuery = '('.implode(' OR ', $orQueries).' ) ';
                $queryBuilder->andWhere($orQuery);
            }
        }
    }
}
