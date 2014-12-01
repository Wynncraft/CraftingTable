<?php

class PluginVersion extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plugin_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['version', 'description'];

    public function plugin() {
        return $this->belongsTo('Plugin');
    }

}