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

    /**
     * Bungee Types
     *
     * @return object
     */
    public function bungeetypes()
    {
        return $this->embedsMany('NetworkBungeeType');
    }

    /**
     * Forced Hosts
     *
     * @return object
     */
    public function forcedhosts()
    {
        return $this->embedsMany('NetworkForcedHost');
    }

    /**
     * Forced Hosts
     *
     * @return object
     */
    public function manualservertypes()
    {
        return $this->embedsMany('NetworkManualServerType');
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
            foreach($network->servers()->get()->all() as $server) {
                $server->delete();
            }
            foreach($network->bungees()->get()->all() as $bungee) {
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
        return $this->servertypes()->where('defaultServerType', '=', true)->first();
    }

    public function getTotalRam() {
        $usableRam = 0;
        $nodes = $this->nodes()->get()->all();
        foreach ($nodes as $node) {
            $usableRam += $node->node()->ram;
        }
        return $usableRam;
    }

    public function getProvisionedRam() {
        $provisionedRam = 0;

        foreach ($this->servertypes()->get() as $servertype) {
            $provisionedRam += $servertype->amount * $servertype->servertype()->ram;
        }

        foreach ($this->bungeetypes()->get() as $bungeetype) {
            $provisionedRam += $bungeetype->amount * $bungeetype->bungeetype()->ram;
        }

        return $provisionedRam;
    }

    public function getUsedRam() {
        $usedRam = 0;
        $servers = $this->servers()->get()->all();
        foreach ($servers as $server) {
            $usedRam += $server->servertype()->ram;
        }
        $bungees = $this->bungees()->get()->all();
        foreach ($bungees as $bungee) {
            $usedRam += $bungee->bungeetype()->ram;
        }
        return $usedRam;
    }

    public function getOnlinePlayers() {
        $players = 0;
        $servers = $this->servers()->get()->all();
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

        $usableRam = $this->getTotalRam();
        $provisionedRam = 0;

        /*$bungeetypes = $this->bungeetypes()->get()->all();
        foreach ($bungeetypes as $bungeetype) {
            $provisionedRam += $bungeetype->amount * $bungeetype->bungeetype()->ram;
        }
        $servertypes = $this->servertypes()->get()->all();
        foreach ($servertypes as $servertype) {
            $provisionedRam += $servertype->amount * $servertype->servertype()->ram;
        }

        if ($provisionedRam > $usableRam) {
            $overProvisioned = true;
        }*/
        $nodes = array();

        foreach ($this->nodes()->get() as $node) {
            $nodes[$node->node()->id.""] = $node->node()->ram;
        }

        $bungeetypes = $this->bungeetypes()->get()->all();
        $setBungees = 0;
        $totalBungees = 0;
        foreach ($bungeetypes as $bungeetype) {
            $totalBungees += $bungeetype->amount;
            foreach ($bungeetype->addresses()->get() as $address) {
                if ($nodes[$address->node()->id.""] >= $bungeetype->bungeetype()->ram) {
                    $nodes[$address->node()->id.""] -= $bungeetype->bungeetype()->ram;
                    $setBungees += 1;
                }
            }
        }

        $servertypes = $this->servertypes()->get()->all();
        $setServers = 0;
        $totalServers = 0;
        foreach ($servertypes as $servertype) {
            $totalServers += $servertype->amount;
            for ($i = 0; $i < $servertype->amount; $i++) {
                foreach ($nodes as $key) {
                    if ($nodes[$key] >= $servertype->servertype()->ram) {
                        $nodes[$key] -= $servertype->servertype()->ram;
                        $setServers += 1;
                        break;
                    }
                }
            }
        }

        if ($setBungees < $totalBungees || $setServers < $totalServers) {
            $overProvisioned = true;
        }

        return $overProvisioned;

    }

    public function hasBungee() {
        $hasBungee = false;

        if ($this->bungeetypes()->count() > 0) {
            $hasBungee = true;
        }

        return $hasBungee;
    }

}