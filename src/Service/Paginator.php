<?php
namespace TurboLabIt\PaginatorBundle\Service;

use TurboLabIt\PaginatorBundle\Exception\PaginatorException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorMissingBaseUrlException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorOverflowException;


class Paginator
{
    protected string $baseUrl;
    protected string $pageParam = 'p';
    protected int $slotNum      = 3;


    public function setBaseUrl(string $unpaginatedUrl) : static
    {
        $this->baseUrl = $unpaginatedUrl;
        return $this;
    }


    public function setSlotNum(int $slotNum) : static
    {
        if( empty($slotNum) || $slotNum < 3 ) {
            throw new PaginatorException('Cannot set the number of visible pages to less than 3 (prev, curr, next');
        }

        $this->slotNum = $slotNum;
        return $this;
    }


    public function build(?int $currentPage, int $totalPages) : \stdClass
    {
        $currentPage = $currentPage ?: 1;
        $this->validateInput($currentPage, $totalPages);

        $arrPaginator = $this->buildBaseNavigation($currentPage, $totalPages);

        // CASE A: 1,2,3,4,5
        // All the pages are visibile, because the page num fits within the visible page interval
        // the "+2" is used to also use the spaces of "..." (before) and "..." (after) as pages
        if( $totalPages <= ($this->slotNum + 2) ) {

            $arrPaginator["pages"] = $this->buildAllPages($currentPage, $totalPages);

            // CASE B: 1,2,3,4,...
            // Some pages are hidden, but the first ones are visible
        } elseif( $currentPage <= $this->slotNum ) {

            $arrPaginator["pages"] = $this->buildAllPages($currentPage, $this->slotNum + 1);
            $arrPaginator["pages"][] = $this->buildItem('...');

            // CASE C: ...,97,98,99,100
            // Some pages are hidden, but the last ones are visible
        } elseif( $currentPage > ($totalPages - $this->slotNum) ) {

            $arrPaginator["pages"] =
                array_merge(
                    [$this->buildItem('...')],
                    $this->buildAllPages($currentPage, $totalPages, $totalPages - $this->slotNum)
                );

            // CASE D: ...,53,54,55,...
            // Some pages are hidden before, some pages are visible, some pages are hidden after
        } else {

            $arrPaginator["pages"] =
                array_merge(
                    [$this->buildItem('...')],
                    $this->buildAllPages($currentPage, $currentPage + 1, $currentPage - 1),
                    [$this->buildItem('...')],
                );
        }

        return (object)$arrPaginator;
    }


    protected function validateInput(?int $currentPage, int $totalPages) : void
    {
        if( empty($currentPage) || $currentPage < 0 ) {
            throw new PaginatorException('The current page cannot be zero or less than zero');
        }

        if( empty($totalPages) || $totalPages < 0 ) {
            throw new PaginatorException('The total number of pages cannot be zero or less than zero');
        }

        if( $currentPage > $totalPages ) {
            throw
                (new PaginatorOverflowException('The current page cannot be greater than the total number of pages'))
                ->setMaxPage($totalPages);
        }

        if( empty($this->baseUrl) ) {
            throw new PaginatorMissingBaseUrlException('The URL to paginate is not set. Invoke setBaseUrl() first.');
        }
    }


    protected function buildBaseNavigation(int $currentPage, int $totalPages) : array
    {
        $currentPage    = $currentPage ?: 1;
        $arrNavigation  = [];

        //
        $urlFirstPage = $currentPage > 1 ? $this->baseUrl : null;
        $arrNavigation["first"] = $this->buildItem('First', $urlFirstPage);

        //
        $urlPrevPage = $currentPage > 1 ? $this->buildUrlWithPageParam($this->baseUrl, $currentPage - 1) : null;
        $arrNavigation["prev"] = $this->buildItem('Prev', $urlPrevPage);

        //
        $arrNavigation['pages'] = [];

        //
        $urlNextPage = $currentPage < $totalPages ? $this->buildUrlWithPageParam($this->baseUrl, $currentPage + 1) : null;
        $arrNavigation["next"] = $this->buildItem('Next', $urlNextPage);

        //
        $lastPageUrl = $currentPage < $totalPages ? $this->buildUrlWithPageParam($this->baseUrl, $totalPages) : null;
        $arrNavigation["last"] = $this->buildItem('Last', $lastPageUrl);

        return $arrNavigation;
    }


    protected function buildAllPages(?int $currentPage, int $totalPages, int $startAt = 1) : array
    {
        $arrPages = [];
        for( $i = $startAt; $i <= $totalPages; $i++ ) {
            $arrPages[] = (object)[
                "label" => $i,
                "url"   => $i == $currentPage ? null : $this->buildUrlWithPageParam($this->baseUrl, $i)
            ];
        }

        return $arrPages;
    }


    public function buildItem(string $label, ?string $url = null) : \stdClass
    {
        $arrItem = [
            "label" => $label,
            "url"   => $url
        ];

        return (object)$arrItem;
    }


    protected function buildUrlWithPageParam(string $unpaginatedUrl, ?int $pageNum) : string
    {
        if( empty($pageNum) || $pageNum == 1 ) {
            return $unpaginatedUrl;
        }

        $unpaginatedUrl .= stripos($unpaginatedUrl, '?') === false ? '?' : '&';

        $urlWithPageParam = $unpaginatedUrl . $this->pageParam . "=" . $pageNum;
        return $urlWithPageParam;
    }
}