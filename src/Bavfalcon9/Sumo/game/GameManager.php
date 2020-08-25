<?php

namespace Bavfalcon9\Sumo\game;

class GameManager
{
    /** @var BaseGame[] */
    private $games;

    public function __construct()
    {
        $this->games = [];
    }

    public function getGame(): BaseGame
    {

    }
}