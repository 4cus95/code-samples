<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;

use Msp\Proactive\Controller\Favourite;

class FavouritesHandler extends AbstractHandler
{
    protected array $favourites;

    public function __construct(array $params)
    {
        parent::__construct($params);

        $favourites = new Favourite();
        $this->favourites = $favourites->get($params['company_id']);
    }

    protected function check(array $item): bool
    {
        if(strlen($item['REGION_SERVICE_ID']) > 0 && !in_array($item['REGION_SERVICE_ID'], $this->favourites['REG_SERVICES'])) {
            return false;
        }

        if(strlen($item['SUPER_SERVICE_ID']) > 0 && !in_array($item['SUPER_SERVICE_ID'], $this->favourites['SUPER_SERVICES'])) {
            return false;
        }

        return true;
    }
}