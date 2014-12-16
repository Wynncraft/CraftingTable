<?php

class PluginHolderPlugin extends Eloquent
{

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
    protected $fillable = ['pluginholder_id', 'pluginholder_type', 'plugin_id', 'pluginversion_id', 'pluginconfig_id'];

    public function plugin() {
        return $this->hasOne('Plugin', 'id', 'plugin_id')->first();
    }

    public function pluginVersion() {
        return $this->hasOne('PluginVersion', 'id', 'pluginversion_id')->first();
    }

    public function pluginConfig() {
        return $this->hasOne('PluginConfig', 'id', 'pluginconfig_id')->first();
    }

    public function pluginholder() {
        return $this->belongsTo('PluginHolder', 'id', 'pluginholder_id');
    }

}