<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 24.04.2017
 * Time: 16:41
 */

namespace AppBundle\Pagination;


class PaginationCollection
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $count;

    private $_links = array();

    public function __construct(array $items, int $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = count($this->items);
    }

    /**
     * adding links to the object
     *
     * @param string $ref
     * @param string $url
     */
    public function addLink(string $ref, string $url) {
        $this->_links[$ref] = $url;
    }
}