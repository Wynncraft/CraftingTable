<?php

class Plugin extends Eloquent {

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

    public function versions() {
        return $this->hasMany('PluginVersion', 'plugin_id');
    }


}