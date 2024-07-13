<?php
namespace TurboLabIt\PaginatorBundle\Exception;

class PaginatorOverflowException extends \Exception
{
    protected int $maxPage;

    public function setMaxPage(int $maxPage) : static
    {
        $this->maxPage = $maxPage;
        return $this;
    }

    public function getMaxPage() : int
    {
        return $this->maxPage;
    }
}
