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
        return $this->hasMany('ServerTypePlugin', 'servertype_id');
    }

    /**
     * Worlds
     *
     * @return object
     */
    public function worlds()
    {
        return $this->hasMany('ServerTypeWorld', 'servertype_id');
    }

    /**
     * Default world
     *
     * @return object
     */
    public function defaultWorld() {
        return $this->worlds()->where('default', '=', '1')->first();
    }


}