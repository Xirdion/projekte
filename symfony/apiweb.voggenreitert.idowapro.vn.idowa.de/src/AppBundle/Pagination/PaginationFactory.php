<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 25.04.2017
 * Time: 09:47
 */

namespace AppBundle\Pagination;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * PaginationFactory constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     * @param string $route
     * @param array $routeParams
     *
     * @return PaginationCollection
     */
    public function createCollection(QueryBuilder $queryBuilder, Request $request, string $route, array $routeParams = array()) {
        $page = $request->query->get('page', 1);

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        $programmers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $programmers[] = $result;
        }

        $paginatedCollection = new PaginationCollection($programmers, $pagerfanta->getNbResults());

        // make sure query parameters are included in pagination links
        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));
        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}