<?php

class World extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'worlds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public function versions() {
        return $this->hasMany('WorldVersion', 'world_id');
    }


}