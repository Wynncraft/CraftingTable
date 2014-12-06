<?php

class NetworkServerType extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'network_servertypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_id', 'server_type_id'];

    public function servertype() {
        return $this->hasOne('ServerType', 'id', 'server_type_id')->first();
    }

    public function network() {
        return $this->belongsTo('Network', 'id', 'network_id');
    }

}