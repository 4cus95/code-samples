<?php


namespace Msp\Proactive\Classes\Request\Filters\Handlers;

use Msp\Proactive\Traits\Constants;
use Msp\Proactive\Traits\Hlblock as HlTrait;

class CardTypeFilter extends AbstractHandler implements Constants
{
    use HlTrait;

    private string $cardType;
    private string $federalCardType;
    private int $regionId;
    private int $supportFederalTypeId;

    /**
     * CardTypeFilter constructor.
     * @param string $cardType
     */
    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->cardType = $this->getEnumIdHlblock(self::HLBLOCK_NAME_CARDS, 'UF_CARD_TYPE', $params['card_type']);
        $this->federalCardType = $this->getEnumIdHlblock(self::HLBLOCK_NAME_CARDS, 'UF_CARD_TYPE', 'FS');
        $this->supportFederalTypeId = $this->getEnumIdHlblock(self::HLBLOCK_REGION_SERVICES_NAME, 'UF_SERVICE_VIEW', 'federal');
        $this->regionId = (int) $params['region_id'];
    }

    protected function check(array $item): bool
    {
        if ($this->cardType == $this->federalCardType) {
            $return = true;
            if ($item['REGION_SERVICE_VIEW'] == $this->supportFederalTypeId) {
                $regionFederalList = $item['REGION_SERVICE_FEDERAL'];
                if ($regionFederalList) {
                    $return = in_array($this->regionId, $regionFederalList);
                }
                return $return;
            }
            return false;
        } else {
            return $item['REGION_SERVICE_VIEW'] && $item['REGION_SERVICE_VIEW'] != $this->supportFederalTypeId;
        }
    }
}