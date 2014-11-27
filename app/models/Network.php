<?php

class Network extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'networks';

    /**
     * Users
     *
     * @return object
     */
    public function nodes()
    {
        return $this->belongsToMany('Node',
            'network_node'
        )->withTimestamps();
    }


}