<?php


namespace Biddy\Model;


class PagerParam
{
    const PARAM_SEARCH_FIELD = 'searchField';
    const PARAM_SEARCH_KEY = 'searchKey';
    const PARAM_SORT_FIELD = 'sortField';
    const PARAM_SORT_DIRECTION = 'orderBy';
    const PARAM_ACCOUNT_ID = 'accountId';
    const PARAM_PAGE = 'page';
    const PARAM_LIMIT = 'limit';
    const PARAM_SEARCHES = 'searches';

    /**
     * @var string
     */
    private $searchField;
    /**
     * @var string
     */
    private $searchKey;
    /**
     * @var string
     */
    private $sortField;
    /**
     * @var string
     */
    private $sortDirection;

    /**
     * @var int
     */
    private $accountId;

    /** @var  int */
    private $page;

    /** @var  int */
    private $limit;

    /** @var  */
    private $searches;

    /**
     * @param $searches
     * @param string $searchField
     * @param string $searchKey
     * @param string $sortField
     * @param string $sortDirection
     * @param int $accountId
     * @param int $page
     * @param int $limit
     */
    function __construct($searches, $searchField = null, $searchKey = null, $sortField = null, $sortDirection = null, $accountId, $page = 1, $limit = 10)
    {
        $this->searches = $searches;
        $this->searchField = $searchField;
        $this->searchKey = $searchKey;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->accountId = $accountId;
        $this->page = $page;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getSearchField()
    {
        if (is_string($this->searchField)) {
            return explode(',', $this->searchField);
        }

        return [];
    }

    /**
     * @param string $searchField
     */
    public function setSearchField($searchField)
    {
        $this->searchField = $searchField;
    }

    /**
     * @return string
     */
    public function getSearchKey()
    {
        return $this->searchKey;
    }

    /**
     * @param string $searchKey
     */
    public function setSearchKey($searchKey)
    {
        $this->searchKey = $searchKey;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return self
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * @param mixed $searches
     * @return self
     */
    public function setSearches($searches)
    {
        $this->searches = $searches;

        return $this;
    }
}