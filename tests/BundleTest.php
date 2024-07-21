<?php
namespace TurboLabIt\PaginatorBundle\Tests;

use TurboLabIt\PaginatorBundle\Exception\PaginatorException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorMissingBaseUrlException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorOverflowException;
use TurboLabIt\PaginatorBundle\Service\Paginator;


class BundleTest extends BaseT
{
    const TESTED_SERVICE_FQN    = 'TurboLabIt\PaginatorBundle\Service\Paginator';
    const BASE_URL              = 'https://test.com';


    protected function getInstance()
    {
        return
            parent::getInstance()
                ->setBaseUrl(static::BASE_URL);
    }


    public function testNewInstance()
    {
        $this->assertInstanceOf(Paginator::class, new Paginator());
        $this->assertInstanceOf(Paginator::class, $this->getInstance());
    }



    public function testBuildFailureWithoutBaseUrl()
    {
        $this->expectException(PaginatorMissingBaseUrlException::class);
        (new Paginator())->build(1, 1);
    }



    public function testNullPages()
    {
        // this must fail due to totalPages
        $this->expectException(\TypeError::class);
        $this->getInstance()->build(null, null);

        // this must fail due to totalPages
        $this->expectException(\TypeError::class);
        $this->getInstance()->build(1, null);

        // this must be fine (page 1 of 1)
        $oPage = $this->getPages(null, 1);
        $this->checkOneOfOne($oPage);
    }


    public function testZeroPages()
    {
        // this must fail due to totalPages
        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(0, 0);

        // this must be fine (page 1 of 1)
        $oPage = $this->getPages(0, 1);
        $this->checkOneOfOne($oPage);
    }


    public function testOverflowPages()
    {
        $this->expectException(PaginatorOverflowException::class);
        $this->getInstance()->build(2, 1);

        $this->expectException(PaginatorOverflowException::class);
        $this->getInstance()->build(3, 2);
    }


    public function testNegativePages()
    {
        // this must fail due to totalPages
        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(0, -1);

        // this must fail due to totalPages
        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(1, -1);

        // this must fail due to totalPages
        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(-1, 0);

        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(-1, 1);

        $this->expectException(PaginatorException::class);
        $this->getInstance()->build(-1, -1);
    }


    public function test1Page()
    {
        // this must be fine (page 1 of 1)
        $oPage = $this->getPages(null, 1);
        $this->checkOneOfOne($oPage);

        $oPages = $this->getPages(1, 1);
        $this->checkOneOfOne($oPage);
    }
}
