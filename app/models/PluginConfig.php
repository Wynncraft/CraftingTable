<?php

class PluginConfig extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public static function boot() {
        parent::boot();

        PluginConfig::deleting(function($pluginConfig) {
            foreach(ServerType::all() as $serverType) {
                foreach($serverType->plugins()->where('pluginconfig_id', '=', $pluginConfig->id) as $serverTypePlugin) {
                    $serverTypePlugin->pluginconfig_id = null;
                    $serverTypePlugin->save();
                }
            }

            return true;
        });
    }

}