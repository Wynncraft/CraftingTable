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
        return $this->belongsToMany('Node',
            'network_node'
        )->withTimestamps();
    }

    /**
     * Server Types
     *
     * @return object
     */
    public function servertypes()
    {
        return $this->belongsToMany('ServerType',
            'network_servertype'
        )->withTimestamps();
    }

    public function overProvisioned() {
        $overProvisioned = false;

        $usableRam = 0;
        $provisionedRam = 0;

        $nodes = $this->nodes()->get()->all();
        foreach ($nodes as $node) {
            $usableRam += $node->ram;
        }

        $servertypes = $this->servertypes()->get()->all();
        foreach ($servertypes as $servertype) {
            $provisionedRam += $servertype->pivot->amount * $servertype->ram;
        }

        if ($provisionedRam > $usableRam) {
            $overProvisioned = true;
        }

        return $overProvisioned;
    }


}