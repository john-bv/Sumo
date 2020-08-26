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
            'Sumo Matches',
            '/sumo <join/leave/list> [id]'
        );
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('sumo.command')) {
            $sender->sendMessage(TF::RED . "You are missing permissions to use this command.");
            return;
        }
        if (count($args) <= 0) {
            $sender->sendMessage(TF::RED . $this->usageMessage);
            return;
        }
        if ($args[0] === 'list') {
            $games = $this->plugin->gameManager->getGames();
            $sender->sendMessage(TF::DARK_GRAY . "===" . Main::NAME . " Current games " . TF::DARK_GRAY . "===" );
            foreach ($games as $game) {
                $map = $game->getMap();
                $sender->sendMessage(TF::GRAY . "" . $map->getName() . ":");
                $sender->sendMessage(" " . TF::GRAY . "Id" . TF::DARK_GRAY . ": " . TF::GRAY . $game->getId());
                $sender->sendMessage(" " . TF::GRAY . "Players" . TF::DARK_GRAY . ": " . TF::GRAY . $game->getPlayerCount());
                $sender->sendMessage(
                    " " . TF::GRAY . "Can Join" . TF::DARK_GRAY . ": " . (($game->canJoin()) ? TF::GREEN . "Yes" : TF::RED . "No")
                );
            }
            return;
        }
        if ($args[0] === 'leave') {
            if (is_null($game = $this->plugin->gameManager->getGameFromPlayer($sender->getName()))) {
                $sender->sendMessage(Main::NAME . TF::RED . "You are not playing any games.");
                return;
            }
            $game->removePlayer($sender->getName());
            $sender->sendMessage(Main::NAME . "You left game: " . $game->getId());
            return;
        }
        if ($args[0] === 'join') {
            if (!isset($args[1])) {
                $sender->sendMessage(Main::NAME . TF::RED . "You must provide a match id.");
                return;
            }
            if (!is_null($game = $this->plugin->gameManager->getGameFromPlayer($sender->getName()))) {
                $sender->sendMessage(Main::NAME . TF::RED . "You are already playing in: " . $game->getId());
                return;
            }
            if (is_null($game = $this->plugin->gameManager->getGame((int) $args[1]))) {
                $sender->sendMessage(Main::NAME . TF::RED . "Could not find that match.");
                return;
            }
            if (!$game->canJoin()) {
                $sender->sendMessage(Main::NAME . TF::RED . "This game is full.");
                return;
            }
            $game->addPlayer($sender->getName());
            $sender->sendMessage(Main::NAME . "You joined game: " . $game->getId());
            return;
        }
        $sender->sendMessage(Main::NAME . "Invalid usage.");
        return;
    }

    public function getPlugin(): Pluginbase {
        return $this->plugin;
    }
}
