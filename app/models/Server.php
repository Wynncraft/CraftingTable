<?php

class Server extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servers';

    public function network() {
        return $this->hasOne('Network', '_id', 'network_id')->first();
    }

    public function node() {
        return $this->hasOne('Node', '_id', 'node_id')->first();
    }

    public function servertype() {
        return $this->hasOne('ServerType', '_id', 'server_type_id')->first();
    }

}