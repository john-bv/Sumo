<?php

namespace Bavfalcon9\Sumo\game;

use pocketmine\Player;

class Team
{
    /** @var string */
    private $name;
    /** @var Player[] */
    private $players;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->players = [];
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Remove a player from the team.
     * @param $search
     * @return bool
     */
    public function removePlayer($search): bool
    {
        if (!$this->hasPlayer($search)) {
            return false;
        }
        foreach ($this->players as $key=>$player) {
            if ($player->getName() === $search) {
                array_splice($this->players, $key, 1);
            }
        }
    }

    /**
     * Gets the player by the a search.
     * @param $search
     * @return Player|null
     */
    public function getPlayer($search): ?Player
    {
        if (($search instanceof Player)) {
            $search = $search->getName();
        }
        foreach ($this->players as $player) {
            if ($player->getName() === $search) {
                return $player;
            }
        }
        return null;
    }

    /**
     * Whether or not the player is in the team.
     * @param $search
     * @return bool
     */
    public function hasPlayer($search): bool
    {
        return !!$this->getPlayer($search);
    }
}