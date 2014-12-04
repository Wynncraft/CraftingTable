<?php

class WorldVersion extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'world_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['version', 'description'];

    public function plugin() {
        return $this->belongsTo('World');
    }

}