<?php

namespace Bavfalcon9\Sumo;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    /** @var EventListener */
    private $events;

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(
            $this->events = new EventListener($this),
            $this
        );
    }

    public function onDisable(): void
    {

    }
}