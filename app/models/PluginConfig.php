<?php

class PluginConfig extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plugin_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public function plugin() {
        return $this->belongsTo('Plugin');
    }

}