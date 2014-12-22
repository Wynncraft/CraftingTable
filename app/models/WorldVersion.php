<?php

class WorldVersion extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['version', 'description'];

    public static function boot() {
        parent::boot();

        WorldVersion::deleting(function($worldVersion) {
            foreach(ServerType::all() as $serverType) {
                foreach($serverType->worlds()->all() as $serverTypeWorld) {
                    if ($serverTypeWorld->worldVersion()->id == $worldVersion->id) {
                        $serverTypeWorld->delete();
                    }
                }
            }

            return true;
        });
    }

}