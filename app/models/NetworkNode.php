<?php

class NetworkNode extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['node_id', 'node_public_address_id', 'bungee_type_id'];

    public function publicaddress() {
        $publicAddress = $this->node()->publicaddresses()->where('id', '=', $this->node_public_address_id)->first();
        return $publicAddress;
    }

    public function bungeetype() {
        return $this->hasOne('BungeeType', '_id', 'bungee_type_id')->first();
    }

    public function node() {
        return $this->hasOne('Node', '_id', 'node_id')->first();
    }

}