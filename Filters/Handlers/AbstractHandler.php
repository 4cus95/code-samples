<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;


class AbstractHandler
{
    protected $next = null;
    protected array $params = [];

    /**
     * AbstractHandler constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function setNext(AbstractHandler $handler): void
    {
        $this->next = $handler;
    }

    protected function check(array $item): bool
    {
        return true;
    }

    public function approved(array $item): bool
    {
        if($this->check($item)) {
            return $this->callNext($item);
        }

        return false;
    }

    protected function callNext(array $item): bool
    {
        if ($this->next instanceof AbstractHandler) {
            return $this->next->approved($item);
        }

        return true;
    }
}