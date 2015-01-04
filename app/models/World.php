<?php

class World extends Moloquent  {

    protected $connection = 'mongodb';

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

    public static function boot() {
        parent::boot();

        World::deleting(function($world) {
            foreach(ServerType::all() as $serverType) {
                foreach($serverType->worlds()->where('world_id', '=', $world->id)->get() as $serverTypeWorld) {
                    $serverTypeWorld->delete();
                }
            }

            return true;
        });
    }

    public function versions() {
        return $this->embedsMany('WorldVersion');
    }


}