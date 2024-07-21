<?php
namespace TurboLabIt\PaginatorBundle\Tests;

use TurboLabIt\PaginatorBundle\Exception\PaginatorException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorMissingBaseUrlException;
use TurboLabIt\PaginatorBundle\Exception\PaginatorOverflowException;
use TurboLabIt\PaginatorBundle\Service\Paginator;


class ModeParamTest extends BaseT
{
    const TESTED_SERVICE_FQN    = 'TurboLabIt\PaginatorBundle\Service\Paginator';
    const BASE_URL              = 'https://test.com';
    const SLOT_NUM              = 3;
    const SLOT_NUM_MORE_SLOTS   = 9;

    protected int $currentSlotNum = -1;


    protected function getInstance()
    {
        return
            parent::getInstance()
                ->setBaseUrl(static::BASE_URL)
                ->setSlotNum($this->currentSlotNum);
    }


    public function testLessThanSlotsPages()
    {
        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(1, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=3","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=3","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false}],"next":{"label":"Next","url":null,"isDots":false},"last":{"label":"Last","url":null,"isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));
    }


    public function testMorePagesThanSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(1, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=5","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=5","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=4","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=5","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(4, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":null,"isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=5","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=5","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":null,"isDots":false}],"next":{"label":"Next","url":null,"isDots":false},"last":{"label":"Last","url":null,"isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesBegin()
    {
        // CASE B: 1,2,3,4,...
        //Some pages are hidden, but the first ones are visible

        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=4","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesEnd()
    {
        // CASE C: ...,97,98,99,100
        //Some pages are hidden, but the last ones are visible

        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages($this->currentSlotNum * 5 - 2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=12","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"12","url":"https:\/\/test.com?p=12","isDots":false},{"label":"13","url":null,"isDots":false},{"label":"14","url":"https:\/\/test.com?p=14","isDots":false},{"label":"15","url":"https:\/\/test.com?p=15","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=14","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5 - 1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=13","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"12","url":"https:\/\/test.com?p=12","isDots":false},{"label":"13","url":"https:\/\/test.com?p=13","isDots":false},{"label":"14","url":null,"isDots":false},{"label":"15","url":"https:\/\/test.com?p=15","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=15","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=14","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"12","url":"https:\/\/test.com?p=12","isDots":false},{"label":"13","url":"https:\/\/test.com?p=13","isDots":false},{"label":"14","url":"https:\/\/test.com?p=14","isDots":false},{"label":"15","url":null,"isDots":false}],"next":{"label":"Next","url":null,"isDots":false},"last":{"label":"Last","url":null,"isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesMiddle()
    {
        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(4, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":null,"isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=5","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":null,"isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=6","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(6, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=5","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":null,"isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=7","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(7, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=6","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":null,"isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=8","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(8, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=7","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":null,"isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=9","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(9, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=8","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":null,"isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=10","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(10, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=9","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":null,"isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=11","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(11, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=10","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":null,"isDots":false},{"label":"12","url":"https:\/\/test.com?p=12","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=12","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(12, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=11","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false},{"label":"12","url":null,"isDots":false},{"label":"13","url":"https:\/\/test.com?p=13","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=13","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=15","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLessThanSlotsPagesWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=9","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=9","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=4","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=9","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));
    }


    public function testMorePagesThanSlotsWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=11","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=11","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=4","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=11","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(4, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":null,"isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=5","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=11","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":null,"isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"11","url":"https:\/\/test.com?p=11","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=6","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=11","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringNotContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesBeginWithMoreSlots()
    {
        // CASE B: 1,2,3,4,...
        //Some pages are hidden, but the first ones are visible

        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":null,"isDots":false},"prev":{"label":"Prev","url":null,"isDots":false},"pages":[{"label":"1","url":null,"isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=2","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":null,"isDots":false},{"label":"3","url":"https:\/\/test.com?p=3","isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=3","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2","isDots":false},"pages":[{"label":"1","url":"https:\/\/test.com","isDots":false},{"label":"2","url":"https:\/\/test.com?p=2","isDots":false},{"label":"3","url":null,"isDots":false},{"label":"4","url":"https:\/\/test.com?p=4","isDots":false},{"label":"5","url":"https:\/\/test.com?p=5","isDots":false},{"label":"6","url":"https:\/\/test.com?p=6","isDots":false},{"label":"7","url":"https:\/\/test.com?p=7","isDots":false},{"label":"8","url":"https:\/\/test.com?p=8","isDots":false},{"label":"9","url":"https:\/\/test.com?p=9","isDots":false},{"label":"10","url":"https:\/\/test.com?p=10","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=4","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesEndWithMoreSlots()
    {
        // CASE C: ...,97,98,99,100
        //Some pages are hidden, but the last ones are visible

        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages($this->currentSlotNum * 5 - 2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=42","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"36","url":"https:\/\/test.com?p=36","isDots":false},{"label":"37","url":"https:\/\/test.com?p=37","isDots":false},{"label":"38","url":"https:\/\/test.com?p=38","isDots":false},{"label":"39","url":"https:\/\/test.com?p=39","isDots":false},{"label":"40","url":"https:\/\/test.com?p=40","isDots":false},{"label":"41","url":"https:\/\/test.com?p=41","isDots":false},{"label":"42","url":"https:\/\/test.com?p=42","isDots":false},{"label":"43","url":null,"isDots":false},{"label":"44","url":"https:\/\/test.com?p=44","isDots":false},{"label":"45","url":"https:\/\/test.com?p=45","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=44","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5 - 1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=43","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"36","url":"https:\/\/test.com?p=36","isDots":false},{"label":"37","url":"https:\/\/test.com?p=37","isDots":false},{"label":"38","url":"https:\/\/test.com?p=38","isDots":false},{"label":"39","url":"https:\/\/test.com?p=39","isDots":false},{"label":"40","url":"https:\/\/test.com?p=40","isDots":false},{"label":"41","url":"https:\/\/test.com?p=41","isDots":false},{"label":"42","url":"https:\/\/test.com?p=42","isDots":false},{"label":"43","url":"https:\/\/test.com?p=43","isDots":false},{"label":"44","url":null,"isDots":false},{"label":"45","url":"https:\/\/test.com?p=45","isDots":false}],"next":{"label":"Next","url":"https:\/\/test.com?p=45","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=44","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"36","url":"https:\/\/test.com?p=36","isDots":false},{"label":"37","url":"https:\/\/test.com?p=37","isDots":false},{"label":"38","url":"https:\/\/test.com?p=38","isDots":false},{"label":"39","url":"https:\/\/test.com?p=39","isDots":false},{"label":"40","url":"https:\/\/test.com?p=40","isDots":false},{"label":"41","url":"https:\/\/test.com?p=41","isDots":false},{"label":"42","url":"https:\/\/test.com?p=42","isDots":false},{"label":"43","url":"https:\/\/test.com?p=43","isDots":false},{"label":"44","url":"https:\/\/test.com?p=44","isDots":false},{"label":"45","url":null,"isDots":false}],"next":{"label":"Next","url":null,"isDots":false},"last":{"label":"Last","url":null,"isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }


    public function testLotsOfPagesMiddleWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(24, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=23","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"20","url":"https:\/\/test.com?p=20","isDots":false},{"label":"21","url":"https:\/\/test.com?p=21","isDots":false},{"label":"22","url":"https:\/\/test.com?p=22","isDots":false},{"label":"23","url":"https:\/\/test.com?p=23","isDots":false},{"label":"24","url":null,"isDots":false},{"label":"25","url":"https:\/\/test.com?p=25","isDots":false},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=25","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(25, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=24","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"21","url":"https:\/\/test.com?p=21","isDots":false},{"label":"22","url":"https:\/\/test.com?p=22","isDots":false},{"label":"23","url":"https:\/\/test.com?p=23","isDots":false},{"label":"24","url":"https:\/\/test.com?p=24","isDots":false},{"label":"25","url":null,"isDots":false},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=26","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(26, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=25","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"22","url":"https:\/\/test.com?p=22","isDots":false},{"label":"23","url":"https:\/\/test.com?p=23","isDots":false},{"label":"24","url":"https:\/\/test.com?p=24","isDots":false},{"label":"25","url":"https:\/\/test.com?p=25","isDots":false},{"label":"26","url":null,"isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=27","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(27, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=26","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"23","url":"https:\/\/test.com?p=23","isDots":false},{"label":"24","url":"https:\/\/test.com?p=24","isDots":false},{"label":"25","url":"https:\/\/test.com?p=25","isDots":false},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":null,"isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"31","url":"https:\/\/test.com?p=31","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=28","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(28, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=27","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"24","url":"https:\/\/test.com?p=24","isDots":false},{"label":"25","url":"https:\/\/test.com?p=25","isDots":false},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":null,"isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"31","url":"https:\/\/test.com?p=31","isDots":false},{"label":"32","url":"https:\/\/test.com?p=32","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=29","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(29, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=28","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"25","url":"https:\/\/test.com?p=25","isDots":false},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":null,"isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"31","url":"https:\/\/test.com?p=31","isDots":false},{"label":"32","url":"https:\/\/test.com?p=32","isDots":false},{"label":"33","url":"https:\/\/test.com?p=33","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=30","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(30, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=29","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"26","url":"https:\/\/test.com?p=26","isDots":false},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":null,"isDots":false},{"label":"31","url":"https:\/\/test.com?p=31","isDots":false},{"label":"32","url":"https:\/\/test.com?p=32","isDots":false},{"label":"33","url":"https:\/\/test.com?p=33","isDots":false},{"label":"34","url":"https:\/\/test.com?p=34","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=31","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(31, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=30","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"27","url":"https:\/\/test.com?p=27","isDots":false},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"31","url":null,"isDots":false},{"label":"32","url":"https:\/\/test.com?p=32","isDots":false},{"label":"33","url":"https:\/\/test.com?p=33","isDots":false},{"label":"34","url":"https:\/\/test.com?p=34","isDots":false},{"label":"35","url":"https:\/\/test.com?p=35","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=32","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));

        $oPages = $this->getPages(32, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com","isDots":false},"prev":{"label":"Prev","url":"https:\/\/test.com?p=31","isDots":false},"pages":[{"label":"...","url":null,"isDots":true},{"label":"28","url":"https:\/\/test.com?p=28","isDots":false},{"label":"29","url":"https:\/\/test.com?p=29","isDots":false},{"label":"30","url":"https:\/\/test.com?p=30","isDots":false},{"label":"31","url":"https:\/\/test.com?p=31","isDots":false},{"label":"32","url":null,"isDots":false},{"label":"33","url":"https:\/\/test.com?p=33","isDots":false},{"label":"34","url":"https:\/\/test.com?p=34","isDots":false},{"label":"35","url":"https:\/\/test.com?p=35","isDots":false},{"label":"36","url":"https:\/\/test.com?p=36","isDots":false},{"label":"...","url":null,"isDots":true}],"next":{"label":"Next","url":"https:\/\/test.com?p=33","isDots":false},"last":{"label":"Last","url":"https:\/\/test.com?p=45","isDots":false}}';
        $this->assertEquals($oExpected, json_encode($oPages));
        $this->assertStringContainsString('"isDots":true', json_encode($oPages));
    }
}
