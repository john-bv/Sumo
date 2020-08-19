<?php

namespace Bavfalcon9\Sumo\game;

use pocketmine\Player;

abstract class BaseGame
{
    /** @var Team[] */
    private $teams;
    /** @var bool */
    protected $running;

    public function __construct()
    {
        $this->teams = [];
        $this->running = false;
    }

    /**
     * Adds a player to the game and gives you the team they are on.
     * @param Player $player
     * @return Team
     */
    public function addPlayer(Player $player): Team
    {
        if (!is_null($team = $this->getTeamByPlayer($player))) {
            return $team;
        }
        if ($this->running) {
            $team = $this->getTeam('Spectator');
            $team->addPlayer($player);
            return $team;
        } else {
            $team = $this->getRandomTeam();
            $team->addPlayer($player);
            return $team;
        }
    }

    /**
     * Removes a player from the game and gives you the team they were on.
     * @param Player $player
     * @return bool
     */
    public function removePlayer(Player $player): bool
    {
        return (($team = $this->getTeamByPlayer($player))) ? $team->removePlayer($player) : false;
    }

    /**
     * Get the Team class for the team that the given player is in.
     * @param Player|string $player
     * @return Team|null
     */
    public function getTeamByPlayer($player): ?Team
    {
        foreach ($this->teams as $team) {
            if ($team->hasPlayer($player)) {
                return $team;
            }
        }
        return null;
    }

    /**
     * Adds a team to the game.
     * @param Team $team
     * @return bool
     */
    public function addTeam(Team $team): bool
    {
        if ($this->getTeam($team->getName())) {
            return false;
        }
        $this->teams[] = $team;
        return true;
    }

    /**
     * Remove a team from the game.
     * @param Team $team
     * @return bool
     */
    public function removeTeam(Team $team): bool
    {
        if (is_null($this->getTeam($team->getName()))) {
            return false;
        }
        foreach ($this->teams as $key=>$tm) {
            if ($tm->getName() === $team->getName()) {
                array_splice($this->teams, $key, 1);
                break;
            }
        }
        return true;
    }

    /**
     * Gets the Team class for the given team name (if any).
     * @param string $name
     * @return Team|null
     */
    public function getTeam(string $name): ?Team
    {
        foreach ($this->teams as $team) {
            if ($team->getName() === $name) {
                return $team;
            }
        }
        return null;
    }

    public function getTeams(): array
    {
        return $this->teams;
    }

    /**
     * This is called every server tick.
     */
    abstract public function tick(): void;
}