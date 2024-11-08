<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;


class StopsHandler extends AbstractHandler
{
    protected function check(array $item): bool
    {
        $criterias = is_array($item['PROACTIVE_CRITERION']) ? $item['PROACTIVE_CRITERION'] : unserialize($item['PROACTIVE_CRITERION']);

        return empty($criterias ?: []);
    }
}