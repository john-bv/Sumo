<?php

namespace Bavfalcon9\Sumo\commands;

use Bavfalcon9\Sumo\game\GameManager;
use Bavfalcon9\Sumo\game\sumo\SumoGame;
use Bavfalcon9\Sumo\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class SumoAdminCommand extends Command
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct(
            'sumoadmin',
            'Manage a Sumo Match',
            '/sumoadmin <create/start/restart/load/end/list> [name/id]'
        );
        $this->setAliases(['sumoa']);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('sumo.admin')) {
            $sender->sendMessage(TF::RED . "You are missing permissions to use this command.");
            return;
        }
        if (count($args) <= 0) {
            $sender->sendMessage(TF::RED . $this->usageMessage);
            return;
        }
        if ($args[0] === 'list') {
            $maps = array_keys($this->plugin->getMaps());
            $sender->sendMessage(TF::DARK_GRAY . "===" . Main::NAME . " Loaded Maps " . TF::DARK_GRAY . "===" );
            $sender->sendMessage(TF::GRAY . "-" . implode("\n-", $maps));
            return;
        }
        if ($args[0] === 'create') {
            if (count($args) < 1) {
                $sender->sendMessage(Main::NAME . TF::RED . "Map name or match id required.");
                return;
            }
            $maps = array_keys($this->plugin->getMaps());
            if (!in_array($args[1], $maps)) {
                $sender->sendMessage(Main::NAME . TF::RED . "The map you requested is not loaded.");
                return;
            }
            if ($this->plugin->gameManager->isPlaying($sender->getName())) {
                $sender->sendMessage(Main::NAME . TF::RED . "You can not start a sumo match while playing.");
                return;
            }
            $map = $this->plugin->getMaps()[$args[1]];
            $game = new SumoGame(
                $this->plugin,
                $map
            );
            $id = $this->plugin->gameManager->registerGame($game);
            $this->plugin->getServer()->broadcastMessage(
                Main::NAME."A new game with map \"$args[1]\" has start with id: $id! Join with /sumo join $id"
            );
            return;
        }
        if ($args[0] === 'restart') {
            if (count($args) < 1) {
                $sender->sendMessage(Main::NAME . TF::RED . "Map name or match id required.");
                return;
            }
            return;
        }
        if ($args[0] === 'end') {
            if (count($args) < 1) {
                $sender->sendMessage(Main::NAME . TF::RED . "Map name or match id required.");
                return;
            }
            return;
        }
        $sender->sendMessage(Main::NAME . "Invalid usage.");
        return;
    }

    public function getPlugin(): Pluginbase {
        return $this->plugin;
    }
}
