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
    const SLOT_NUM              = 3;


    protected function getInstance()
    {
        return
            parent::getInstance()
                ->setBaseUrl(static::BASE_URL)
                ->setSlotNum(static::SLOT_NUM);
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


    protected function getPages(?int $currentPage, int $totalPages) : \stdClass
    {
        $oPaginator = $this->getInstance();
        $oPages     = $oPaginator->build($currentPage, $totalPages);
        $this->assertIsObject($oPages);
        foreach(['first', 'prev', 'pages', 'next', 'last'] as $key) {
            $this->assertObjectHasProperty($key, $oPages);
        }

        return $oPages;
    }


    protected function checkOneOfOne(\stdClass $oPages)
    {
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
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


    public function testLessThanSlotsPages()
    {
        $oPages = $this->getPages(1, static::SLOT_NUM);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=3"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, static::SLOT_NUM);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=3"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, static::SLOT_NUM);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testAllPages()
    {
        $oPages = $this->getPages(1, 5);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(4, 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":null},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=5"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(5, 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesBegin()
    {
        // CASE B: 1,2,3,4,...
        //Some pages are hidden, but the first ones are visible

        $oPages = $this->getPages(1, 15);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesEnd()
    {
        // CASE C: ...,97,98,99,100
        //Some pages are hidden, but the last ones are visible

        $oPages = $this->getPages(13, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=12"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":null},{"label":14,"url":"https:\/\/test.com?p=14"},{"label":15,"url":"https:\/\/test.com?p=15"}],"next":{"label":"Next","url":"https:\/\/test.com?p=14"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(14, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=13"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":14,"url":null},{"label":15,"url":"https:\/\/test.com?p=15"}],"next":{"label":"Next","url":"https:\/\/test.com?p=15"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(15, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=14"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":14,"url":"https:\/\/test.com?p=14"},{"label":15,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesMiddle()
    {
        $oPages = $this->getPages(4, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3"},"pages":[{"label":"...","url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":null},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=5"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(5, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4"},"pages":[{"label":"...","url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":null},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=6"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(6, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=5"},"pages":[{"label":"...","url":null},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":null},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=7"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(7, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=6"},"pages":[{"label":"...","url":null},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":null},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=8"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(8, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=7"},"pages":[{"label":"...","url":null},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":null},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=9"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(9, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=8"},"pages":[{"label":"...","url":null},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":null},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=10"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(10, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=9"},"pages":[{"label":"...","url":null},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":null},{"label":11,"url":"https:\/\/test.com?p=11"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=11"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(11, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=10"},"pages":[{"label":"...","url":null},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=12"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(12, 15);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=11"},"pages":[{"label":"...","url":null},{"label":11,"url":"https:\/\/test.com?p=11"},{"label":12,"url":null},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=13"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }
}
