<?php

class NetworkBungeeTypeAddress extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['node_id', 'node_public_address_id'];

    public function node() {
        return $this->hasOne('Node', '_id', 'node_id')->first();
    }

    /*public function nodePublicAddress() {
        return $this->hasOne('NodePublicAddress', '_id', 'node_public_address_id')->first();
    }*/

}