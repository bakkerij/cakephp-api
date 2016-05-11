<?php

namespace Api\Pagination;

use Cake\Controller\Component\PaginatorComponent;
use Cake\Network\Request;
use Cake\Routing\Router;
use League\Fractal\Pagination\PaginatorInterface;

class CakePaginatorAdapter implements PaginatorInterface
{

    /**
     * The paginator instance.
     *
     * @var PaginatorComponent
     */
    protected $paginator;

    /**
     * The Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The type to paginate on. Gotten from the Request params.
     * @var string
     */
    protected $pagingType;

    /**
     * CakePaginatorAdapter constructor.
     *
     * @param PaginatorComponent $paginator
     * @param Request $request
     */
    public function __construct(PaginatorComponent $paginator, Request $request)
    {
        $this->paginator = $paginator;
        $this->request = $request;

        $paging = $request->param('paging');
        $this->pagingType = key($paging);
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->request->param('paging.'.$this->pagingType.'.page');
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->request->param('paging.'.$this->pagingType.'.pageCount');
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->request->param('paging.'.$this->pagingType.'.count');
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
            return $this->request->param('paging.'.$this->pagingType.'.count');
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->request->param('paging.'.$this->pagingType.'.perPage');
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        $url = Router::parse(Router::url());

        $url['page'] = $page;

        return Router::fullBaseUrl() . Router::url($url);
    }
}