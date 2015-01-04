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
                foreach($serverType->worlds()->where('worldversion_id', '=', $worldVersion->id)->get() as $serverTypeWorld) {
                    $serverTypeWorld->worldversion_id = null;
                    $serverTypeWorld->save();
                }
            }

            return true;
        });
    }

}