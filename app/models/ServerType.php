<?php

class ServerType extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servertypes';

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
        return $this->belongsToMany('Plugin',
            'servertype_plugin'
        )->withTimestamps();
    }


}