<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;


class HasCardType extends AbstractHandler
{
    protected function check(array $item): bool
    {
        return (bool)$item['CARD_TYPE'];
    }
}