<?php

class ServerTypeWorld extends Moloquent
{
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['servertype_id', 'world_id', 'worldversion_id'];

    public function world() {
        return $this->hasOne('World', '_id', 'world_id')->first();
    }

    public function worldVersion() {
        $worldVersion = $this->world()->versions()->where('id', '=', $this->worldversion_id)->first();
        return $worldVersion;
    }

}