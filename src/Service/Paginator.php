<?php
namespace TurboLabIt\PaginatorBundle\Service;

use stdClass;
use TurboLabIt\PaginatorBundle\Exception\PaginatorException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorMissingBaseUrlException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorOverflowException;


class Paginator
{
    const MODE_PARAM                            = 'param';
    const MODE_LAST_SLUG                        = 'last-slug';
    const MODE_LAST_SLUG_WITH_TRAILING_SLASH    = 'last-slug-with-trailing-slash';

    protected string $baseUrl;
    protected string $baseUrlForPages;
    protected string $pageParam         = 'p';
    protected int $slotNum              = 5;
    protected int $itemsPerPage         = 25;
    protected string $mode              = self::MODE_PARAM;


    //<editor-fold defaultstate="collapsed" desc="CUSTOMIZABLE OPTIONS">
    public function setBaseUrl(string $unpaginatedUrl) : static
    {
        $this->baseUrl = $unpaginatedUrl;
        return $this;
    }


    public function setBaseUrlForPages(string $unpaginatedUrl) : static
    {
        $this->baseUrlForPages = $unpaginatedUrl;
        return $this;
    }


    public function setSlotNum(int $slotNum) : static
    {
        if( empty($slotNum) || $slotNum < 3 ) {
            throw new PaginatorException('Cannot set the number of visible pages to less than 3 (prev, curr, next)');
        }

        $this->slotNum = $slotNum;
        return $this;
    }


    public function setItemsPerPageNum(int $itemsNum) : static
    {
        if( empty($itemsNum) || $itemsNum < 1 ) {
            throw new PaginatorException('Cannot set the number of items per page to less than 1');
        }

        $this->itemsPerPage = $itemsNum;
        return $this;
    }
    //</editor-fold>


    //<editor-fold defaultstate="collapsed" desc="🏗️ BUILD METHODS 🏗️">
    public function build(?int $currentPage, int $totalPages) : stdClass
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


    public function buildByTotalItems(?int $currentPage, int $totalItems) : stdClass
    {
        $totalPages = (int)ceil($totalItems / $this->itemsPerPage);
        $totalPages = empty($totalPages) ? 1 : $totalPages;
        return $this->build($currentPage, $totalPages);
    }
    //</editor-fold>


    //<editor-fold defaultstate="collapsed" desc="PUBLIC GETTERS">
    public function getItemsPerPageNum() : int { return $this->itemsPerPage; }


    public function getBaseUrl(int $pageNum = 1) : string
    {
        $pageNum = empty($pageNum) ? 1 : $pageNum;

        if( $pageNum < 0 ) {
            throw new PaginatorException('Cannot build the baseUrl for a negative page');
        }

        if( $pageNum == 1 && empty($this->baseUrl) ) {
            throw new PaginatorMissingBaseUrlException("baseUrl not set");
        }

        if( $pageNum == 1 || empty($this->baseUrlForPages) ) {
            return $this->baseUrl;
        }

        return $this->baseUrlForPages;
    }
    //</editor-fold>


    //<editor-fold defaultstate="collapsed" desc="INTERNAL METHODS">
    protected function validateInput(?int $currentPage, int $totalPages) : void
    {
        if( empty($currentPage) || $currentPage < 0 ) {
            throw new PaginatorException('The current page cannot be zero or less');
        }

        if( empty($totalPages) || $totalPages < 0 ) {
            throw new PaginatorException('The total number of pages cannot be zero or less');
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
        $urlPrevPage = $currentPage > 1 ? $this->buildUrlWithPageParam($currentPage - 1) : null;
        $arrNavigation["prev"] = $this->buildItem('Prev', $urlPrevPage);

        //
        $arrNavigation['pages'] = [];

        //
        $urlNextPage = $currentPage < $totalPages ? $this->buildUrlWithPageParam($currentPage + 1) : null;
        $arrNavigation["next"] = $this->buildItem('Next', $urlNextPage);

        //
        $lastPageUrl = $currentPage < $totalPages ? $this->buildUrlWithPageParam($totalPages) : null;
        $arrNavigation["last"] = $this->buildItem('Last', $lastPageUrl);

        return $arrNavigation;
    }


    protected function buildAllPages(?int $currentPage, int $totalPages, int $startAt = 1) : array
    {
        $arrPages = [];
        for( $i = $startAt; $i <= $totalPages; $i++ ) {
            $arrPages[] = (object)[
                "label" => $i,
                "url"   => $i == $currentPage ? null : $this->buildUrlWithPageParam($i)
            ];
        }

        return $arrPages;
    }


    protected function buildItem(string $label, ?string $url = null) : stdClass
    {
        $arrItem = [
            "label" => $label,
            "url"   => $url
        ];

        return (object)$arrItem;
    }


    protected function buildUrlWithPageParam(?int $pageNum) : string
    {
        $unpaginatedUrl = $this->getBaseUrl($pageNum);

        if( empty($pageNum) || $pageNum == 1 ) {
            return $unpaginatedUrl;
        }

        if( $this->mode == static::MODE_PARAM ) {

            $unpaginatedUrl    .= stripos($unpaginatedUrl, '?') === false ? '?' : '&';
            $urlWithPageParam   = $unpaginatedUrl . $this->pageParam . "=" . $pageNum;
            return $urlWithPageParam;
        }

        if( in_array($this->mode, [static::MODE_LAST_SLUG, static::MODE_LAST_SLUG_WITH_TRAILING_SLASH]) ) {

            $urlWithPageParam = $unpaginatedUrl;

            if( substr($urlWithPageParam, -1) != '/' ) {
                $urlWithPageParam .= '/';
            }

            $urlWithPageParam .= $pageNum;

            if( $this->mode == static::MODE_LAST_SLUG_WITH_TRAILING_SLASH ) {
                $urlWithPageParam .= "/";
            }

            return $urlWithPageParam;
        }

        throw new PaginatorException('Unknown Pagination Mode used');
    }
    //</editor-fold>
}
