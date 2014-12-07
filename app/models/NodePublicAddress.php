<?php

class NodePublicAddress extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'node_public_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['node_id', 'public_address'];

    public function node() {
        return $this->belongsTo('Node', 'id', 'node_id');
    }
}