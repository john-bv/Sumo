<?php

namespace Bavfalcon9\Sumo;

use Bavfalcon9\Sumo\commands\SumoAdminCommand;
use Bavfalcon9\Sumo\commands\SumoCommand;
use Bavfalcon9\Sumo\game\GameManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
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
        PermissionManager::getInstance()->addPermission(
            new Permission('sumo.command', 'Allow access to sumo default commands', Permission::DEFAULT_TRUE)
        );
        PermissionManager::getInstance()->addPermission(
            new Permission('sumo.admin', 'Allow access to sumo admin commands', Permission::DEFAULT_OP)
        );
        $this->getserver()->getCommandMap()->registerAll('sumo', [
            new SumoCommand($this),
            new SumoAdminCommand($this)
        ]);
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
     * @param int[] $spawns
     * @param Level $level
     * @return Vector3|null
     */
    public function getPosition(array $spawns, Level $level): ?Position
    {
        if (!isset($spawns['x']) || !isset($spawns['y']) || !isset($spawns['z'])) {
            return null;
        }
        return new Position($spawns['x'], $spawns['y'], $spawns['z'], $level);
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
        $maps = $this->getConfig()->getAll();
        foreach ($maps as $mapName=>$mapData) {
            if ($mapName === 'Hub') {
                continue;
            }
            try {
                $map = Map::fromSave($this, $mapName, $mapData);
                $this->maps[$mapName] = $map;
                $this->getLogger()->debug("Sumo map: " . $mapName . " loaded successfully.");
            } catch (\Throwable $exception) {
                $this->getLogger()->error($exception->getMessage());
            }
        }
    }
}