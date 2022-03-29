<?php

use App\Controller\HomeController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::get("/", [HomeController::class,"index"]);