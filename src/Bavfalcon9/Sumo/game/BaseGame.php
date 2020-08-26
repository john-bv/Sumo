<?php

namespace Bavfalcon9\Sumo\game;

use Bavfalcon9\Sumo\Main;
use Bavfalcon9\Sumo\Map;
use pocketmine\Player;
use pocketmine\event\Listener;

class BaseGame implements Listener
{
    /** @var Main */
    protected $plugin;
    /** @var Map */
    protected $map;
    /** @var string[] */
    protected $players;
    /** @var int */
    protected $maxPlayers;
    /** @var bool */
    protected $running;
    /** @var int */
    private $id;

    public function __construct(Main $plugin, Map $map, int $maxPlayers)
    {
        $this->plugin = $plugin;
        $this->map = $map;
        $this->players = [];
        $this->maxPlayers = $maxPlayers;
        $this->running = false;
        $this->id = 0;
    }

    /**
     * Starts the game.
     * @return bool
     */
    public function start(): bool
    {
        return false;
    }

    /**
     * Stops the game
     * @return bool
     */
    public function stop(): bool
    {
        return false;
    }

    /**
     * Adds a player to the game. (this does not respect whether they can join)
     * @param string $player
     */
    public function addPlayer(string $player): void
    {
        if ($this->hasPlayer($player)) return;
        $this->players[] = $player;
    }

    /**
     * Removes a player from the game.
     * @param string $player
     */
    public function removePlayer(string $player): void
    {
       if (!$this->hasPlayer($player)) return;
       array_splice($this->players, array_search($player, $this->players));
    }

    /**
     * Checks whether or not the game has a certain player.
     * @param string $player
     * @return bool
     */
    public function hasPlayer(string $player): bool
    {
        return in_array($player, $this->players);
    }

    /**
     * Gets the current amount of players participating in the game
     * For this plugin, this also includes spectators.
     * @return int
     */
    public function getPlayerCount(): int
    {
        return count($this->players);
    }

    /**
     * Gets the current players in the game.
     * @return string[]
     */
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

    /**
     * Whether or not players can join this game.
     * @return bool
     */
    public function canJoin(): bool
    {
        return count($this->players) >= $this->maxPlayers;
    }

    /**
     * Whether or not the game is running.
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->running;
    }

    /**
     * Sets the game id
     * @param int $id
     * @return int
     */
    public function setId(int $id): int
    {
        if ($this->id !== 0) {
            return $this->id;
        } else {
            return $this->id = $id;
        }
    }

    /**
     * Gets the game id.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getMap(): Map
    {
        return $this->map;
    }
}