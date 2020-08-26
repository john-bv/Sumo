<?php

namespace Bavfalcon9\Sumo\game;

use Bavfalcon9\Sumo\Main;

class GameManager
{
    /** @var Main */
    private $plugin;
    /** @var BaseGame[] */
    private $games;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->games = [];
    }

    /**
     * Gets a game based on the id.
     * @param int $id
     * @return BaseGame|null
     */
    public function getGame(int $id): ?BaseGame
    {
        foreach ($this->games as $game) {
            if ($game->getId() === $id) {
                return $game;
            }
        }
        return null;
    }

    /**
     * Gets current running games.
     * @return BaseGame[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    /**
     * Gets a game based on player.
     * @param string $player
     * @return BaseGame|null
     */
    public function getGameFromPlayer(string $player): ?BaseGame
    {
        foreach ($this->games as $game) {
            if ($game->hasPlayer($player)) {
                return $game;
            }
        }
        return null;
    }

    /**
     * Whether or not the player is playing a game.
     * @param string $player
     * @return bool
     */
    public function isPlaying(string $player): bool
    {
        return !is_null($this->getGameFromPlayer($player));
    }

    /**
     * Creates a new game
     * @param BaseGame $game
     * @return int
     */
    public function registerGame(BaseGame $game): int
    {
        $this->plugin->getServer()->getPluginManager()->registerEvents($game, $this->plugin);
        $game->setId($id = (mt_rand(100, 999) + $this->games));
        $this->games[] = $game;
        return $id;
    }

    /**
     * Removes a game from the game manager.
     * @param int $id
     * @return bool
     */
    public function removeGame(int $id): bool
    {
        foreach ($this->games as $k=>$game) {
            if ($game->getId() === $id) {
                if ($game->isRunning()) {
                    $game->stop();
                }
                unset($this->games[$k]);
                return true;
            }
        }
        return false;
    }
}