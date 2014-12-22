<?php

class PluginHolderPlugin extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pluginholder_plugins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['plugin_id', 'pluginversion_id', 'pluginconfig_id'];

    public function plugin() {
        return $this->hasOne('Plugin', '_id', 'plugin_id')->first();
    }

    public function pluginVersion() {
        $pluginVersion = $this->plugin()->versions()->where('id', '=', $this->pluginversion_id)->first();
        return $pluginVersion;
    }

    public function pluginConfig() {
        $pluginConfig = $this->plugin()->configs()->where('id', '=', $this->pluginconfig_id)->first();
        return $pluginConfig;
    }

}