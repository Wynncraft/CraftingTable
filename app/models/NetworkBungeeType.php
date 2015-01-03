<?php

class NetworkBungeeType extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['bungee_type_id'];

    public function bungeetype() {
        return $this->hasOne('BungeeType', '_id', 'bungee_type_id')->first();
    }

}