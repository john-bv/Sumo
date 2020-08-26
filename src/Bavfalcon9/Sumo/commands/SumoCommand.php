<?php

namespace Bavfalcon9\Sumo\commands;

use Bavfalcon9\Sumo\game\GameManager;
use Bavfalcon9\Sumo\game\sumo\SumoGame;
use Bavfalcon9\Sumo\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class SumoCommand extends Command
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct(
            'sumo',
            'Start/Manage a Sumo Match',
            '/sumo <start/join/restart/end/list/maplist> [name/id]'
        );
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (count($args) <= 0) {
            $sender->sendMessage(TF::RED . $this->usageMessage);
            return;
        }
        if ($args[0] === 'list') {
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(TF::RED . $this->usageMessage);
            return;
        }
        if ($args[0] === 'start') {
            $maps = array_keys($this->plugin->getMaps());
            if (!in_array($args[0], $maps)) {
                $sender->sendMessage(TF::RED . "The map you requested is not loaded.");
                return;
            }
            if ($this->plugin->gameManager->isPlaying($sender->getName())) {
                $sender->sendMessage(TF::RED . "You can not start a sumo match while playing.");
                return;
            }
            $map = $this->plugin->getMaps()[$args[0]];
            $game = new SumoGame(
                $this->plugin,
                $map->getPlayerSpawns()[0],
                $map->getPlayerSpawns()[1],
                $map->getSpectatorSpawn(),
                $map->getMaxPlayers()
            );
            $id = $this->plugin->gameManager->registerGame($game);
            $this->plugin->getServer()->broadcastMessage(
                Main::NAME."A new game with map \"$args[0]\" has start with id: $id! Join with /sumo join $id"
            );
            return;
        }
        if ($args[0] === 'restart') {
            return;
        }
        if ($args[0] === 'end') {
            return;
        }
    }

    public function getPlugin(): Pluginbase {
        return $this->plugin;
    }
}
