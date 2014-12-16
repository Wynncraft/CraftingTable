<?php

class ServerType extends PluginHolder {

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
        return $this->worlds()->where('defaultWorld', '=', '1')->first();
    }


}