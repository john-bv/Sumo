<?php

namespace Bavfalcon9\Sumo\game;

use pocketmine\Player;
use Bavfalcon9\Sumo\Main;

class BaseGame
{
    /** @var Main */
    protected $plugin;
    /** @var string[] */
    protected $players;
    /** @var int */
    protected $maxPlayers;
    /** @var bool */
    protected $running;

    public function __construct(Main $plugin, int $maxPlayers)
    {
        $this->plugin = $plugin;
        $this->players = [];
        $this->maxPlayers = $maxPlayers;
        $this->running = false;
    }

    public function addPlayer(string $player): void
    {
        if (!$this->hasPlayer($player)) return;
        $this->players[] = $player;
    }

    public function removePlayer(string $player): void
    {
       if (!$this->hasPlayer($player)) return;
       array_splice($this->players, array_search($player, $this->players));
    }

    public function hasPlayer(string $player): bool
    {
        return isset($this->players[$player]);
    }

    public function getPlayerCount(): int
    {
        return count($this->players);
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Gets an array of players as player instances.
     * @return Player[]
     */
    public function getOnlinePlayers(): array
    {
        $players = [];
        foreach ($this->players as $player) {
            if (!is_null($ol = $this->plugin->getServer()->getPlayerExact($player))) {
                $players[] = $ol;
            } else {
                $this->removePlayer($player);
            }
        }
        return $players;
    }

    public function canJoin(): bool
    {
        return count($this->players) >= $this->maxPlayers;
    }
}