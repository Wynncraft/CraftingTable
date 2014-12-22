<?php

class ServerType extends PluginHolder {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servertypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public static function boot() {
        parent::boot();

        ServerType::deleting(function($servertype) {
            foreach(Network::all() as $network) {
                foreach ($network->servertypes()->all() as $networkServerType) {
                    if ($networkServerType->servertype()->id == $servertype->id) {
                        $networkServerType->delete();
                    }
                }
                foreach($network->servers() as $server) {
                    $server->delete();
                }
            }

            return true;
        });
    }

    /**
     * Worlds
     *
     * @return object
     */
    public function worlds()
    {
        return $this->embedsMany('ServerTypeWorld');
    }

    /**
     * Default world
     *
     * @return object
     */
    public function defaultWorld() {
        return $this->worlds()->where('defaultWorld', '=', '1')->first();
    }


}