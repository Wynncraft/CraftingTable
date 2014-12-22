<?php

class Network extends Moloquent {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'networks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * Nodes
     *
     * @return object
     */
    public function nodes()
    {
        return $this->embedsMany('NetworkNode');
    }

    /**
     * Server Types
     *
     * @return object
     */
    public function servertypes()
    {
        return $this->embedsMany('NetworkServerType');
    }

    public function servers() {
        return $this->hasMany('Server');
    }

    public function bungees() {
        return $this->hasMany('Bungee');
    }

    public static function boot() {
        parent::boot();

        Network::deleting(function($network) {
            foreach($network->servers() as $server) {
                $server->delete();
            }
            foreach($network->bungees() as $bungee) {
                $bungee->delete();
            }

            return true;
        });
    }

    /**
     * Default world
     *
     * @return object
     */
    public function defaultServerType() {
        return $this->servertypes()->where('defaultServerType', '=', '1')->first();
    }

    public function getTotalRam() {
        $usableRam = 0;
        $nodes = $this->nodes()->get()->all();
        foreach ($nodes as $node) {
            $usableRam += $node->node()->ram;
            if ($node->bungeetype() != null) {
                $usableRam -= $node->bungeetype()->ram;
            }
        }
        return $usableRam;
    }

    public function getUsedRam() {
        $usedRam = 0;
        $servers = $this->servers();
        $bungees = $this->bungees();
        foreach ($servers as $server) {
            $usedRam += $server->servertype()->ram;
        }
        foreach ($bungees as $bungee) {
            $usedRam += $bungee->bungeetype()->ram;
        }
        return $usedRam;
    }

    public function getOnlinePlayers() {
        $players = 0;
        $servers = $this->servers();
        foreach ($servers as $server) {
            $players += $server->players;
        }
        return $players;
    }

    public function getTotalPlayers() {
        $slots = 0;
        $servertypes = $this->servertypes()->get()->all();
        foreach($servertypes as $servertype) {
            $slots += $servertype->servertype()->players*$servertype->amount;
        }
        return $slots;
    }

    public function overProvisioned() {
        $overProvisioned = false;

        $usableRam = 0;
        $provisionedRam = 0;

        $nodes = $this->nodes()->get()->all();
        foreach ($nodes as $node) {
            $usableRam += $node->node()->ram;
        }

        $servertypes = $this->servertypes()->get()->all();
        foreach ($servertypes as $servertype) {
            $provisionedRam += $servertype->amount * $servertype->servertype()->ram;
        }

        if ($provisionedRam > $usableRam) {
            $overProvisioned = true;
        }

        return $overProvisioned;
    }

    public function hasBungee() {
        $hasBungee = false;

        $nodes = $this->nodes()->get()->all();
        foreach ($nodes as $node) {
            if ($node->bungeetype() != null) {
                $hasBungee = true;
                break;
            }
        }

        return $hasBungee;
    }

}