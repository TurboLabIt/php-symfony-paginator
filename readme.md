# [turbolabit/paginatorbundle](https://github.com/TurboLabIt/php-symfony-paginator)

A simple Symfony bundle to render the "classic" pagination element.

It also works without Symfony, as a plain PHP object.


## 📦 Install it with composer

````shell
symfony composer require turbolabit/paginatorbundle:dev-main
````


## 🏗️ Use it


**src/Service/Paginator.php**

````php
<?php
namespace App\Service;

use \TurboLabIt\PaginatorBundle\Service\Paginator as BasePaginator;


class Paginator extends BasePaginator
{
    protected string $pageParam = 'p';
    protected int $slotNum      = 5;
}
````


**src/Controller/ListingController.php**

````php
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\Paginator;


class ListingController extends AbstractController
{
    protected Request $request;


    public function __construct(RequestStack $requestStack, protected Paginator $paginator)
    {
        $this->request = $requestStack->getCurrentRequest();
    }


    #[Route('/{categorySlug}/', name: 'app_listing', priority: -99)]
    public function listing(string $wpCategorySlug) : Response
    {
        $currentPage     = $this->request->get('p') ?? 1; 
        $totalPages      = 99;
        $oPages          = $this->paginator->build($currentPage, $totalPages);

        return $this->render('listing.html.twig', [
            'page'      => $currentPage,
            'Pages'     => $oPages
        ]);
    }
}
````


## 🧪 Test it

````shell
bash scripts/symfony-bundle-tester.sh
````
