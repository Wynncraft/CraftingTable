<?php

class PluginVersion extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['version', 'description'];

    public static function boot() {
        parent::boot();

        PluginVersion::deleting(function($pluginVersion) {
            foreach(ServerType::all() as $serverType) {
                foreach($serverType->plugins()->all() as $serverTypePlugin) {
                    if ($serverTypePlugin->pluginVersion()->id == $pluginVersion->id) {
                        $serverTypePlugin->delete();
                    }
                }
            }

            return true;
        });
    }

}