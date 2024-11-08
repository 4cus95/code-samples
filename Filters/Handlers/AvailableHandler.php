<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;

use Bitrix\Main\Type\DateTime;

class AvailableHandler extends AbstractHandler
{
    protected function check(array $item): bool
    {
        $now = new DateTime();

        if($item['REGION_SUPPORT_DATE_END'] instanceof DateTime) {
            if($item['REGION_SUPPORT_DATE_END'] < $now) {
                return false;
            }
        }

        if($item['REGION_SUPPORT_DATE_BEGIN'] instanceof DateTime) {
            if($item['REGION_SUPPORT_DATE_BEGIN'] > $now) {
                return false;
            }
        }

        return true;
    }
}