<?php

class ServerTypeWorld extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servertype_worlds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['servertype_id', 'world_id', 'worldversion_id'];

    public function world() {
        return $this->hasOne('World', 'id', 'world_id')->first();
    }

    public function worldVersion() {
        return $this->hasOne('WorldVersion', 'id', 'worldversion_id')->first();
    }

    public function serverType() {
        return $this->belongsTo('ServerType', 'id', 'servertype_id');
    }

}