<?php

class BungeeType extends PluginHolder {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bungeetypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

}