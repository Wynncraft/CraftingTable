<?php

class NetworkForcedHost extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['host', 'server_type_id'];

    public function servertype() {
        return $this->hasOne('ServerType', '_id', 'server_type_id')->first();
    }

}