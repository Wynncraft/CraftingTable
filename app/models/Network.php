<?php

class Network extends Eloquent {

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
        return $this->hasMany('NetworkNode', 'network_id');
    }

    /**
     * Server Types
     *
     * @return object
     */
    public function servertypes()
    {
        return $this->hasMany('NetworkServerType', 'network_id');
    }

    /**
     * Default world
     *
     * @return object
     */
    public function defaultServerType() {
        return $this->servertypes()->where('default', '=', '1')->first();
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