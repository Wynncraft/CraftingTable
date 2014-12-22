<?php

class Bungee extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bungees';

    public function network() {
        return $this->hasOne('Network', '_id', 'network_id')->first();
    }

    public function node() {
        return $this->hasOne('Node', '_id', 'node_id')->first();
    }

    public function publicAddress() {
        $publicAddress = $this->node()->publicaddresses()->where('_id', '=', $this->publicAddress)->first();
        return $publicAddress;
    }

    public function servertype() {
        return $this->hasOne('ServerType', '_id', 'server_type_id')->first();
    }

}