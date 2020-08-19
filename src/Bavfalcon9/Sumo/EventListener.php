<?php

namespace Bavfalcon9\Sumo;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        // TODO finish this
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        // TODO finish this
    }
}