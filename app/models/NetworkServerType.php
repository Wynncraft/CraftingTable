<?php

class NetworkServerType extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['server_type_id'];

    public static function boot() {
        parent::boot();

        NetworkServerType::deleting(function($networkservertype) {

            Log::info("Network ".$networkservertype->network);
            foreach ($networkservertype->network->forcedhosts()->get() as $forcedhost) {
                if ($forcedhost->servertype()->id == $networkservertype->servertype()->id) {
                    $forcedhost->server_type_id = null;
                    $forcedhost->save();
                }
            }

            return true;
        });
    }

    public function servertype() {
        return $this->hasOne('ServerType', '_id', 'server_type_id')->first();
    }

}