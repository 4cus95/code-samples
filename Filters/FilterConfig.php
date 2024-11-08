<?php


namespace Msp\Proactive\Classes\Request\Filters;


use Msp\Proactive\Classes\Request\Filters\Handlers\DirectionHandler;
use Msp\Proactive\Classes\Request\Filters\Handlers\FavouritesHandler;
use Msp\Proactive\Classes\Request\Filters\Handlers\StopsHandler;
use Msp\Proactive\Traits\Constants;
use Msp\Proactive\Traits\Hlblock as HlTrait;
use Msp\Proactive\Classes\Request\Filters\Handlers\AbstractHandler;
use Msp\Proactive\Classes\Request\Filters\Handlers\HasCardType;
use Msp\Proactive\Classes\Request\Filters\Handlers\CardTypeFilter;
use Msp\Proactive\Classes\Request\Filters\Handlers\AvailableHandler;

class FilterConfig implements Constants
{
    use HlTrait;

    protected array $params = [];

    /**
     * FilterConfig constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function compose(): array
    {
        $result = [
            [
                'handler' => HasCardType::class,
                'params' => []
            ],
        ];

        $this->addCardTypeFilter($result);
        $this->addAvailableFilter($result);
        $this->addStopsHandler($result);
        $this->addFavouritesHandler($result);
        $this->addSupportDirectionHandler($result);

        return $result;
    }

    protected function addCardTypeFilter(&$result): void
    {
        $cardType = htmlspecialcharsbx($this->params['CARD_TYPE']);
        $regionId = htmlspecialcharsbx($this->params['REGION']);
        if (in_array($cardType, [self::REGION_SERVICE_CARD_TYPE, self::FEDERAL_SERVICE_CARD_TYPE])) {
            $result[] = [
                'handler' => CardTypeFilter::class,
                'params' => [
                    'card_type' => $cardType,
                    'region_id' => $regionId,
                ]
            ];
        }
    }

    protected function addAvailableFilter(&$result): void
    {
        if(htmlspecialcharsbx($this->params['AVAILABLE_NOW']) == 'Y') {
            $result[] = [
                'handler' => AvailableHandler::class,
                'params' => [

                ]
            ];
        }
    }

    protected function addStopsHandler(&$result): void
    {
        if(htmlspecialcharsbx($this->params['WITH_STOPS']) == 'N') {
            $result[] = [
                'handler' => StopsHandler::class,
                'params' => [

                ]
            ];
        }
    }

    protected function addFavouritesHandler(&$result): void
    {
        if(htmlspecialcharsbx($this->params['ONLY_FAVOURITES']) == 'Y') {
            $result[] = [
                'handler' => FavouritesHandler::class,
                'params' => [
                    'company_id' => $this->params['COMPANY_ID']
                ]
            ];
        }
    }

    protected function addSupportDirectionHandler(&$result): void
    {
        if (is_array($this->params['SUPPORT_DIRECTION']) && count($this->params['SUPPORT_DIRECTION']) > 0) {
            $result[] = [
                'handler' => DirectionHandler::class,
                'params' => [
                    'support_directions' => $this->params['SUPPORT_DIRECTION']
                ]
            ];
        }
    }
}