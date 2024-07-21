<?php
namespace TurboLabIt\PaginatorBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;


abstract class BaseT extends TestCase
{
    const TESTED_SERVICE_FQN = null;

    protected static KernelT $kernelT;


    protected function setUp() :void
    {
        static::$kernelT = new KernelT('test', true);
        static::$kernelT->boot();
    }


    protected function getInstance()
    {
        $service = $this->getService(static::TESTED_SERVICE_FQN);
        $this->assertInstanceOf(static::TESTED_SERVICE_FQN, $service);
        return $service;
    }


    protected static function getService(string $name)
    {
        $container = static::$kernelT->getContainer();
        $service = $container->get($name);
        return $service;
    }


    protected function checkOneOfOne(\stdClass $oPages)
    {
        $oExpected = '{"first":{"label":"First","url":null},"prev":{"label":"Prev","url":null},"pages":[{"label":1,"url":null}],"next":{"label":"Next","url":null},"last":{"label":"Last","url":null}}';
        $this->assertEquals($oExpected, json_encode($oPages));
    }


    protected function getPages(?int $currentPage, int $totalPages) : \stdClass
    {
        $oPages     = $this->getInstance()->build($currentPage, $totalPages);
        $this->assertIsObject($oPages);
        foreach(['first', 'prev', 'pages', 'next', 'last'] as $key) {
            $this->assertObjectHasProperty($key, $oPages);
        }

        return $oPages;
    }
}


//<editor-fold defaultstate="collapsed" desc="*** SYMFONY KERNEL ***">
use Symfony\Component\HttpKernel\Kernel;
use TurboLabIt\PaginatorBundle\TurboLabItPaginatorBundle;

class KernelT extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TurboLabItPaginatorBundle()
        ];
    }
}
//</editor-fold>
