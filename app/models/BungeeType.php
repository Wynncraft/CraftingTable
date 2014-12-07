<?php

class BungeeType extends Eloquent {

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

    /**
     * Plugins
     *
     * @return object
     */
    public function plugins()
    {
        return $this->hasMany('BungeeTypePlugin', 'bungeetype_id');
    }


}