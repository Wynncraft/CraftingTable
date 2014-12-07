<?php

class BungeeTypePlugin extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bungeetype_plugins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['bungeetype_id', 'plugin_id', 'pluginversion_id'];

    public function plugin() {
        return $this->hasOne('Plugin', 'id', 'plugin_id')->first();
    }

    public function pluginVersion() {
        return $this->hasOne('PluginVersion', 'id', 'pluginversion_id')->first();
    }

    public function bungeeType() {
        return $this->belongsTo('BungeeType', 'id', 'bungeetype_id');
    }

}