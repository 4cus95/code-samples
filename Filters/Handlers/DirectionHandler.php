<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;


use Bitrix\Main\Loader;
use Msp\Proactive\Traits\Constants;
use Msp\Proactive\Traits\Hlblock as HlTrait;
use Nota\Hl\Highload\Highload;
use NotaMsp\Helpers\Tools;

class DirectionHandler extends AbstractHandler implements Constants
{
    use HlTrait;

    private array $supportDirections;
    private array $whiteOkweds;

    /**
     * CardTypeFilter constructor.
     * @param string $cardType
     */
    public function __construct(array $params)
    {
        Loader::includeModule('nota.hl');

        parent::__construct($params);

        $this->supportDirections = $this->getSupportIds();

        $okwedEntity = Highload::getDataClassByTableName('nota_okwed2');
        $arOkwed2 = $okwedEntity::getList([
            'filter' => [
                '=UF_SUPPORT_DIRECTION' => $this->supportDirections
            ],
            'select' => ['UF_CODE']
        ])->fetchAll();

        $this->whiteOkweds = array_column($arOkwed2, 'UF_CODE');
    }

    protected function getSupportIds(): array
    {
        $arSupportDirectionIds = [];
        if ($this->params['support_directions']) {
            $arSupportDirectionIds = array_column(
                Highload::getDataClassByTableName('msp_region_support_direction')::getList([
                    'select' => ['ID'],
                    'filter' => ['=UF_XML_ID' => $this->params['support_directions']],
                    'cache' => ['ttl' => Tools::TIME_DAY]
                ])->fetchAll(),
                'ID'
            );
        }

        $this->supportDirections = $arSupportDirectionIds;

        return $arSupportDirectionIds;
    }

    protected function check(array $item): bool
    {
        return $this->hasSupportDirectionCrossing($item) || $this->filterSupportDirectionOkveds($item);
    }

    private function hasSupportDirectionCrossing(array $item): bool
    {
        $crossing = array_intersect($this->supportDirections, array_column($item['REGION_SERVICE_SUPPORT_DIRECTION'] ?: [], 'ID'));

        return !empty($crossing);
    }

    protected function filterSupportDirectionOkveds(array $item): bool
    {
        if(empty($this->whiteOkweds)) {
            return false;
        }

        if (strlen(trim($item['REGION_SERVICE_OKVED'])) > 0) {
            $cardOkweds = preg_replace('/\s+/', '', $item['REGION_SERVICE_OKVED']);
            $cardOkweds = explode(',', $cardOkweds);
            $crossing = array_intersect($cardOkweds, $this->whiteOkweds);
            if (count($crossing) <= 0) {
                return false;
            }
        } else { //Если окведа нет
            return false;
        }

        return true;
    }
}