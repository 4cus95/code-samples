<?php


namespace Msp\Proactive\Classes\Request\Filters;

use Msp\Proactive\Classes\Request\Filters\Handlers\AbstractHandler;

class ChainFilter
{
    private array $elements;
    private FilterConfig $config;

    public function __construct(array $elements, FilterConfig $config)
    {
        $this->elements = $elements;
        $this->config = $config;
    }

    public function get(): array
    {
        $arConfig = $this->config->compose();
        $runHandler = $this->composeHandlers($arConfig);

        if (!($runHandler instanceof AbstractHandler)) {
            return $this->elements;
        }

        foreach ($this->elements as $index => $element) {
            if (!$runHandler->approved($element)) {
                unset($this->elements[$index]);
            }
        }

        return $this->elements;
    }

    protected function composeHandlers(array $arConfig): ?AbstractHandler
    {
        $runHandler = null;
        $currentHandler = null;

        foreach ($arConfig as $handler) {
            $newHandler = new $handler['handler']($handler['params']);

            if (!$runHandler) {
                $runHandler = $newHandler;
            }

            if ($currentHandler) {
                $currentHandler->setNext($newHandler);
            }

            $currentHandler = $newHandler;
        }

        return $runHandler;
    }
}