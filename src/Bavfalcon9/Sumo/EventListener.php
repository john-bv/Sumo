<?php

namespace Bavfalcon9\Sumo;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;


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
        $event->getPlayer();
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        // TODO finish this
    }
}