<?php

class Node extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'nodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'private_address', 'ram'];

    /**
     * Public Addresses
     *
     * @return object
     */
    public function publicaddresses()
    {
        return $this->hasMany('NodePublicAddress', 'node_id');
    }
}