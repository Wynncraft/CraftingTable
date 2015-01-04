<?php

class Plugin extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plugins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public static function boot() {
        parent::boot();

        Plugin::deleting(function($plugin) {
            foreach(ServerType::all() as $serverType) {
                foreach($serverType->plugins()->where('plugin_id', '=', $plugin->id)->get() as $serverTypePlugin) {
                    $serverTypePlugin->delete();
                }
            }

            return true;
        });
    }

    public function versions() {
        return $this->embedsMany('PluginVersion');
    }

    public function configs() {
        return $this->embedsMany('PluginConfig');
    }
}