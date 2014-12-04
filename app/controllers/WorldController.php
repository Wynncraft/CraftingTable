<?php

class WorldController extends BaseController
{

    public function getWorlds()
    {
        return View::make('worlds');
    }

}