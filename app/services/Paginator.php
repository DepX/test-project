<?php

namespace Acme\Service;

use Simple\DI\InjectionInterface;

class Paginator implements InjectionInterface
{
    private $di;

    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @param \Simple\Mvc\Model $query
     * @param $template
     * @param int $page
     * @param int $maxResult
     * @return array
     */
    public function generate($query, $template, $page = 1, $maxResult = 20)
    {
        // all results
        $query->getQuery();
        $executeAll = $query->execute();
        $totalCount = $this->di->getService('db')->count($executeAll);
        $totalPages = ceil($totalCount / $maxResult);

        // results for limit
        $page = ($page <= 0) ? 1 : $page;
        $query->limit(($page - 1) * $maxResult . ', ' . $maxResult);
        $query->getQuery();
        $execute = $query->execute();

        $items = $this->di->getService('db')->getAll($execute, $query);

        return [
            'items' => $items,
            'navigation' => $this->di->getService('view')->partial($template, [
                'current_page' => $page,
                'total_count' => $totalCount,
                'total_pages' => $totalPages,
                'maxResult' => $maxResult
            ])
        ];
    }
}