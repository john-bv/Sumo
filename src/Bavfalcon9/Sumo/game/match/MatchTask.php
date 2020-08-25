<?php

namespace Bavfalcon9\Sumo\game\match;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;
use Bavfalcon9\Sumo\game\sumo\SumoGame;

class MatchTask extends Task
{
    /** @var SumoGame */
    private $game;
    /** @var int */
    private $time;
    /** @var int */
    private $maxTime;
    /** @var int */
    private $countDown;

    /**
     * MatchTask constructor.
     * @param SumoGame $game
     * @param int $maxTime - How long a single sumo match should last.
     * @param int $countDown - How long the countdown is.
     */
     public function __construct(SumoGame $game, int $maxTime = 0, int $countDown = 5)
     {
         $this->game = $game;
         $this->time = 0;
         $this->maxTime = $maxTime;
         $this->countDown = $countDown;
     }

     public function onRun(int $tick): void
     {
         if ($this->countDown > 0) {
             foreach ($this->game->getOnlinePlayers() as $player) {
                 // TODO Config values for this
                 $title = TF::RED . TF::BOLD . "{$this->countDown}";
                 $subtitle = TF::RED . "Match: " . implode(' VS ', $this->game->getCurrentPlayers());
                 $player->addTitle($title, $subtitle);
                 // TODO Sound
             }
             $this->countDown--;
             return;
         }
         if ($this->time >= $this->maxTime) {
             $this->game->endCurrentMatch();
             return;
         }
         foreach ($this->game->getOnlinePlayers() as $player) {
             $player->sendTip('Time: ' . ($this->maxTime - $this->time));
         }
     }
}