<?php

namespace Bavfalcon9\Sumo;

use Bavfalcon9\Sumo\util\MapException;
use pocketmine\level\Level;
use pocketmine\level\Position;

class Map
{
    /** @var string */
    private $name;
    /** @var string */
    private $world;
    /** @var int */
    private $deathY;
    /** @var int */
    private $maxPlayers;
    /** @var int */
    private $time;
    /** @var bool */
    private $protect;
    /** @var Position */
    private $spectatorSpawn;
    /** @var Position[] */
    private $playerSpawns;

    /**
     * @param Main $plugin
     * @param string $name
     * @param array $mapData
     * @throws MapException
     * @return Map
     */
    public static function fromSave(Main $plugin, string $name, array $mapData): Map {
        $validMapKeys = ["world", "deathY", "players", "time", "protection", "spawns"];
        foreach ($validMapKeys as $key) {
            if (!isset($mapData[$key])) {
                throw new MapException("$name is missing key: \"$key\"");
            }
        }
        if (is_null($plugin->getServer()->getLevelByName($mapData['world']))) {
            if (!$plugin->getServer()->loadLevel($mapData['world'])) {
                throw new MapException("$name Failed to load because the provided world for this match does not exist.");
            }
        }
        $level = $plugin->getServer()->getLevelByName($mapData['world']);
        $playerSpawns = [];
        if (!isset($mapData['spawns']['spectator']) || !isset($mapData['spawns']['players'])) {
            throw new MapException("$name Failed to load because one of spawnpoints: \"players\", \"spectator\" do not exist.");
        }
        foreach ($mapData['spawns']['players'] as $id=>$spawn) {
            $playerSpawns[] = $plugin->getPosition($spawn, $level);
        }
        $specSpawn = $plugin->getPosition($mapData['spawns']['spectator'], $level);
        return new self(
            $name,
            $mapData['world'],
            $mapData['deathY'],
            $mapData['players'],
            $mapData['time'],
            $specSpawn,
            $playerSpawns
        );
    }

    public function __construct(string $name, string $world, int $deathY, int $maxPlayers, int $time, Position $spos, array $ppos)
    {
        $this->name = $name;
        $this->world = $world;
        $this->deathY = $deathY;
        $this->maxPlayers = $maxPlayers;
        $this->time = $time;
        $this->protect = true;
        $this->spectatorSpawn = $spos;
        $this->playerSpawns = $ppos;
    }

    /**
     * Change whether or not to protect the map.
     * @param bool $value
     * @return bool
     */
    public function setProtected(bool $value): bool
    {
        return $this->protect = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWorld(): string
    {
        return $this->world;
    }

    public function getDeathY(): int
    {
        return $this->deathY;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getProtected(): bool
    {
        return $this->protect;
    }

    public function getSpectatorSpawn(): Position
    {
        return $this->spectatorSpawn;
    }

    /**
     * @return Position[]
     */
    public function getPlayerSpawns(): array
    {
        return $this->playerSpawns;
    }
}