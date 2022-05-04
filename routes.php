<?php

use App\Controller\HomeController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::get("/validateToken", [HomeController::class,"validateToken"]);
SimpleRouter::get("/", [HomeController::class,"index"]);
