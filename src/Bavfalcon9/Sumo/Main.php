<?php

namespace Bavfalcon9\Sumo;

use Bavfalcon9\Sumo\game\GameManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase
{
    public const NAME = TF::DARK_GRAY . "[" . TF::RED . "SUMO" . TF::DARK_GRAY . "] " . TF::GRAY;
    /** @var GameManager */
    public $gameManager;
    /** @var Position */
    public $hubSpawn;
    /** @var Map[] */
    private $maps;

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->gameManager = new GameManager($this);
        $this->maps = [];
        $this->loadMatches();
    }

    public function onDisable(): void
    {
        $this->maps = [];
        foreach ($this->gameManager->getGames() as $game) {
            $game->stop();
        }
    }

    /**
     * @return Map[]
     */
    public function getMaps(): array
    {
        return $this->maps;
    }

    /**
     * Gets a position from a array of coordinates
     * @param int[] $key
     * @param Level $level
     * @return Vector3|null
     */
    public function getPosition(array $key, Level $level): ?Position
    {
        $xyz = explode('', 'xyz');
        // Integrity check
        foreach ($xyz as $pos) {
            if (!isset($key[$pos])) {
                return null;
            }
        }
        return new Position($key['x'], $key['y'], $key['z'], $level);
    }

    /**
     * Loads the hub.
     */
    private function loadHub(): bool
    {
        // Load hub spawn
        if (!($hubSpawn = $this->getConfig()->get('Hub'))) {
            $this->getLogger()->error("\"Hub\" key in config not found. This is required for teleportation after a match ends.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        if (!isset($hubSpawn['world'])) {
            $this->getLogger()->error("Can not find world for: Hub");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        if (!isset($hubSpawn['spawn'])) {
            $this->getLogger()->error("You are missing the \"spawn\" for hub.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        if (is_null($this->getServer()->getLevelByName($hubSpawn['world']))) {
            if (!$this->getServer()->loadLevel($hubSpawn['world'])) {
                $this->getLogger()->error("World for the Hub is invalid and could not be loaded. Make sure you double check the world's name.");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return false;
            }
        }
        $level = $this->getServer()->getLevelByName($hubSpawn['world']);
        $this->hubSpawn = $this->getPosition($hubSpawn['spawn'], $level);
        if (is_null($this->hubSpawn)) {
            $this->getLogger()->error("Hub failed to load!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    }

    /**
     * Loads all matches by name.
     */
    private function loadMatches(): void
    {
        if (!$this->loadHub()) return;
        $maps = $this->getConfig()->getAll(true);
        foreach ($maps as $mapName=>$mapData) {
            if ($mapName === 'Hub') {
                continue;
            }
            try {
                $map = Map::fromSave($this, $mapName, $mapData);
                $this->maps[$mapName] = $map;
            } catch (\Throwable $exception) {
                $this->getLogger()->error($exception->getMessage());
            }
        }
    }
}