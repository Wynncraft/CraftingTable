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
                foreach($serverType->plugins()->where('pluginversion_id', '=', $pluginVersion->id) as $serverTypePlugin) {
                    $serverTypePlugin->pluginversion_id = null;
                    $serverTypePlugin->save();
                }
            }

            return true;
        });
    }

}