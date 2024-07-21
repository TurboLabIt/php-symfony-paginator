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
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=3"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=3"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testMorePagesThanSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(1, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(4, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":null},{"label":5,"url":"https:\/\/test.com?p=5"}],"next":{"label":"Next","url":"https:\/\/test.com?p=5"},"last":{"label":"Last","url":"https:\/\/test.com?p=5"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesBegin()
    {
        // CASE B: 1,2,3,4,...
        //Some pages are hidden, but the first ones are visible

        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesEnd()
    {
        // CASE C: ...,97,98,99,100
        //Some pages are hidden, but the last ones are visible

        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages($this->currentSlotNum * 5 - 2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=12"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":null},{"label":14,"url":"https:\/\/test.com?p=14"},{"label":15,"url":"https:\/\/test.com?p=15"}],"next":{"label":"Next","url":"https:\/\/test.com?p=14"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5 - 1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=13"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":14,"url":null},{"label":15,"url":"https:\/\/test.com?p=15"}],"next":{"label":"Next","url":"https:\/\/test.com?p=15"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=14"},"pages":[{"label":"...","url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":14,"url":"https:\/\/test.com?p=14"},{"label":15,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesMiddle()
    {
        $this->currentSlotNum = static::SLOT_NUM;

        $oPages = $this->getPages(4, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3"},"pages":[{"label":"...","url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":null},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=5"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4"},"pages":[{"label":"...","url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":null},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=6"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(6, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=5"},"pages":[{"label":"...","url":null},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":null},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=7"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(7, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=6"},"pages":[{"label":"...","url":null},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":null},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=8"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(8, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=7"},"pages":[{"label":"...","url":null},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":null},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=9"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(9, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=8"},"pages":[{"label":"...","url":null},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":null},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=10"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(10, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=9"},"pages":[{"label":"...","url":null},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":null},{"label":11,"url":"https:\/\/test.com?p=11"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=11"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(11, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=10"},"pages":[{"label":"...","url":null},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":null},{"label":12,"url":"https:\/\/test.com?p=12"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=12"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(12, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=11"},"pages":[{"label":"...","url":null},{"label":11,"url":"https:\/\/test.com?p=11"},{"label":12,"url":null},{"label":13,"url":"https:\/\/test.com?p=13"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=13"},"last":{"label":"Last","url":"https:\/\/test.com?p=15"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLessThanSlotsPagesWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=9"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=9"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=9"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testMorePagesThanSlotsWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":"https:\/\/test.com?p=11"}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=11"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":"https:\/\/test.com?p=11"}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=11"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":"https:\/\/test.com?p=11"}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=11"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(4, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=3"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":null},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":"https:\/\/test.com?p=11"}],"next":{"label":"Next","url":"https:\/\/test.com?p=5"},"last":{"label":"Last","url":"https:\/\/test.com?p=11"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(5, $this->currentSlotNum + 2);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=4"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":null},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":11,"url":"https:\/\/test.com?p=11"}],"next":{"label":"Next","url":"https:\/\/test.com?p=6"},"last":{"label":"Last","url":"https:\/\/test.com?p=11"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesBeginWithMoreSlots()
    {
        // CASE B: 1,2,3,4,...
        //Some pages are hidden, but the first ones are visible

        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=2"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":null},{"label":3,"url":"https:\/\/test.com?p=3"},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=3"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(3, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=2"},"pages":[{"label":1,"url":"https:\/\/test.com"},{"label":2,"url":"https:\/\/test.com?p=2"},{"label":3,"url":null},{"label":4,"url":"https:\/\/test.com?p=4"},{"label":5,"url":"https:\/\/test.com?p=5"},{"label":6,"url":"https:\/\/test.com?p=6"},{"label":7,"url":"https:\/\/test.com?p=7"},{"label":8,"url":"https:\/\/test.com?p=8"},{"label":9,"url":"https:\/\/test.com?p=9"},{"label":10,"url":"https:\/\/test.com?p=10"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=4"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesEndWithMoreSlots()
    {
        // CASE C: ...,97,98,99,100
        //Some pages are hidden, but the last ones are visible

        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages($this->currentSlotNum * 5 - 2, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=42"},"pages":[{"label":"...","url":null},{"label":36,"url":"https:\/\/test.com?p=36"},{"label":37,"url":"https:\/\/test.com?p=37"},{"label":38,"url":"https:\/\/test.com?p=38"},{"label":39,"url":"https:\/\/test.com?p=39"},{"label":40,"url":"https:\/\/test.com?p=40"},{"label":41,"url":"https:\/\/test.com?p=41"},{"label":42,"url":"https:\/\/test.com?p=42"},{"label":43,"url":null},{"label":44,"url":"https:\/\/test.com?p=44"},{"label":45,"url":"https:\/\/test.com?p=45"}],"next":{"label":"Next","url":"https:\/\/test.com?p=44"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5 - 1, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=43"},"pages":[{"label":"...","url":null},{"label":36,"url":"https:\/\/test.com?p=36"},{"label":37,"url":"https:\/\/test.com?p=37"},{"label":38,"url":"https:\/\/test.com?p=38"},{"label":39,"url":"https:\/\/test.com?p=39"},{"label":40,"url":"https:\/\/test.com?p=40"},{"label":41,"url":"https:\/\/test.com?p=41"},{"label":42,"url":"https:\/\/test.com?p=42"},{"label":43,"url":"https:\/\/test.com?p=43"},{"label":44,"url":null},{"label":45,"url":"https:\/\/test.com?p=45"}],"next":{"label":"Next","url":"https:\/\/test.com?p=45"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages($this->currentSlotNum * 5, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=44"},"pages":[{"label":"...","url":null},{"label":36,"url":"https:\/\/test.com?p=36"},{"label":37,"url":"https:\/\/test.com?p=37"},{"label":38,"url":"https:\/\/test.com?p=38"},{"label":39,"url":"https:\/\/test.com?p=39"},{"label":40,"url":"https:\/\/test.com?p=40"},{"label":41,"url":"https:\/\/test.com?p=41"},{"label":42,"url":"https:\/\/test.com?p=42"},{"label":43,"url":"https:\/\/test.com?p=43"},{"label":44,"url":"https:\/\/test.com?p=44"},{"label":45,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    public function testLotsOfPagesMiddleWithMoreSlots()
    {
        $this->currentSlotNum = static::SLOT_NUM_MORE_SLOTS;

        $oPages = $this->getPages(24, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=23"},"pages":[{"label":"...","url":null},{"label":20,"url":"https:\/\/test.com?p=20"},{"label":21,"url":"https:\/\/test.com?p=21"},{"label":22,"url":"https:\/\/test.com?p=22"},{"label":23,"url":"https:\/\/test.com?p=23"},{"label":24,"url":null},{"label":25,"url":"https:\/\/test.com?p=25"},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=25"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(25, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=24"},"pages":[{"label":"...","url":null},{"label":21,"url":"https:\/\/test.com?p=21"},{"label":22,"url":"https:\/\/test.com?p=22"},{"label":23,"url":"https:\/\/test.com?p=23"},{"label":24,"url":"https:\/\/test.com?p=24"},{"label":25,"url":null},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=26"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(26, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=25"},"pages":[{"label":"...","url":null},{"label":22,"url":"https:\/\/test.com?p=22"},{"label":23,"url":"https:\/\/test.com?p=23"},{"label":24,"url":"https:\/\/test.com?p=24"},{"label":25,"url":"https:\/\/test.com?p=25"},{"label":26,"url":null},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=27"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(27, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=26"},"pages":[{"label":"...","url":null},{"label":23,"url":"https:\/\/test.com?p=23"},{"label":24,"url":"https:\/\/test.com?p=24"},{"label":25,"url":"https:\/\/test.com?p=25"},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":null},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":31,"url":"https:\/\/test.com?p=31"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=28"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(28, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=27"},"pages":[{"label":"...","url":null},{"label":24,"url":"https:\/\/test.com?p=24"},{"label":25,"url":"https:\/\/test.com?p=25"},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":null},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":31,"url":"https:\/\/test.com?p=31"},{"label":32,"url":"https:\/\/test.com?p=32"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=29"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(29, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=28"},"pages":[{"label":"...","url":null},{"label":25,"url":"https:\/\/test.com?p=25"},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":null},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":31,"url":"https:\/\/test.com?p=31"},{"label":32,"url":"https:\/\/test.com?p=32"},{"label":33,"url":"https:\/\/test.com?p=33"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=30"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(30, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=29"},"pages":[{"label":"...","url":null},{"label":26,"url":"https:\/\/test.com?p=26"},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":null},{"label":31,"url":"https:\/\/test.com?p=31"},{"label":32,"url":"https:\/\/test.com?p=32"},{"label":33,"url":"https:\/\/test.com?p=33"},{"label":34,"url":"https:\/\/test.com?p=34"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=31"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(31, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=30"},"pages":[{"label":"...","url":null},{"label":27,"url":"https:\/\/test.com?p=27"},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":31,"url":null},{"label":32,"url":"https:\/\/test.com?p=32"},{"label":33,"url":"https:\/\/test.com?p=33"},{"label":34,"url":"https:\/\/test.com?p=34"},{"label":35,"url":"https:\/\/test.com?p=35"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=32"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));

        $oPages = $this->getPages(32, $this->currentSlotNum * 5);
        $oExpected = '{"first":{"label":"First","url":"https:\/\/test.com"},"prev":{"label":"Prev","url":"https:\/\/test.com?p=31"},"pages":[{"label":"...","url":null},{"label":28,"url":"https:\/\/test.com?p=28"},{"label":29,"url":"https:\/\/test.com?p=29"},{"label":30,"url":"https:\/\/test.com?p=30"},{"label":31,"url":"https:\/\/test.com?p=31"},{"label":32,"url":null},{"label":33,"url":"https:\/\/test.com?p=33"},{"label":34,"url":"https:\/\/test.com?p=34"},{"label":35,"url":"https:\/\/test.com?p=35"},{"label":36,"url":"https:\/\/test.com?p=36"},{"label":"...","url":null}],"next":{"label":"Next","url":"https:\/\/test.com?p=33"},"last":{"label":"Last","url":"https:\/\/test.com?p=45"}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }
}
