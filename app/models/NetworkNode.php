<?php

class NetworkNode extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'network_nodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_id', 'node_id', 'node_public_address_id', 'bungee_type_id'];

    public function publicaddress() {
        return $this->hasOne('NodePublicAddress', 'id', 'node_public_address_id')->first();
    }

    public function bungeetype() {
        return $this->hasOne('BungeeType', 'id', 'bungee_type_id')->first();
    }

    public function node() {
        return $this->hasOne('Node', 'id', 'node_id')->first();
    }

    public function network() {
        return $this->belongsTo('Network', 'id', 'network_id');
    }

}