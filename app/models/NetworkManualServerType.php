<?php

class NetworkManualServerType extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'address', 'port'];

    public static function boot() {
        parent::boot();

        NetworkManualServerType::deleting(function($networkservertype) {

            Log::info("Network ".$networkservertype->network);
            foreach ($networkservertype->network->forcedhosts()->get() as $forcedhost) {
                if ($forcedhost->server_type_id == $networkservertype->id) {
                    $forcedhost->server_type_id = null;
                    $forcedhost->save();
                }
            }

            return true;
        });
    }

}