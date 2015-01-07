<?php

class NetworkController extends BaseController {

    public function postNetwork() {

        if (Auth::user()->can('create_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to create networks');
        }

        $network = Network::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$network->name,
                'description'=>Input::get('description')),
            array('name'=>'required|min:3|max:100|unique:networks',
                'description'=>'max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('errorAdd', $validator->messages());
        } else {

            $network->description = Input::get('description');
            $network->save();

            return Redirect::to('/')->with('open'.$network->id, 'successAdd')->with('success', 'Created network '.$network->name);
        }

    }

    public function putNetwork(Network $network = null) {

        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description')),
            array('name'=>'required|min:3|max:100|unique:networks,'.$network->id,
                'description'=>'max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorEdit')->with('errorEdit'.$network->id, $messages);
        } else {
            $network->name = Input::get('name');
            $network->description = Input::get('description');
            $network->save();

            return Redirect::to('/')->with('open'.$network->id, 'successEdit')->with('success', 'Saved network '.$network->name);
        }
    }

    public function deleteNetwork(Network $network = null) {

        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('delete_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to delete networks');
        }

        $network->delete();

        return Redirect::to('/')->with('success', 'Deleted network '.$network->name);
    }

    public function putServerType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        foreach ($network->servertypes()->get() as $serverType) {
            $amount = Input::get($network->id.'amount'.$serverType->id);
            $default = Input::get($network->id.'default'.$serverType->id, false);
            $manual = Input::get($network->id.'manual'.$serverType->id, false);

            $serverType->amount = $amount;
            if ($default == "1") {
                $serverType->defaultServerType = true;
            } else {
                $serverType->defaultServerType = $default;
            }
            if ($manual == "1") {
                $serverType->manualStart = true;
            } else {
                $serverType->manualStart = $manual;
            }

            Validator::extend('multiDefault', function($attribute, $value, $parameters) use($serverType, $network) {
                $isDefault = 0;

                foreach ($network->servertypes()->get() as $testType) {
                    if (Input::has($network->id . 'default' . $testType->id)) {
                        $isDefault += 1;
                    }
                }

                if ($isDefault > 1) {
                    return false;
                }

                return true;
            }, 'Please select only one default server type.');

            $validator = Validator::make(
                array('default'=>$default),
                array('default'=>'multiDefault')
            );

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorUpdateServerType')->with('errorUpdateServerType'.$network->id, $validator->messages());
            }

            $serverType->save();
        }

        return Redirect::to('/')->with('open'.$network->id, 'successUpdateServerType')->with('success', 'Updated server types for the network '.$network->name);
    }

    public function postServerType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $servertype = ServerType::find($value);

            if ($servertype == null) {
                return false;
            }

            return true;
        }, 'Please select a valid server type');

        $validator = Validator::make(
            array('servertype'=>Input::get('servertype')),
            array('servertype'=>'required|checkType')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
        } else {
            $servertype = ServerType::find(Input::get('servertype'));
            $networkServerType = new NetworkServerType(array('network_id'=>$network->id, 'server_type_id'=>$servertype->id));

            Validator::extend('servertypeExists', function($attribute, $value, $parameters) use($network) {

                if ($network->servertypes()->where('server_type_id', '=', $value->server_type_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The server type '.$servertype->name.' is already added');

            $validator = Validator::make(
                array('serverType'=>$networkServerType),
                array('serverType'=>'servertypeExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
            }

            //$networkServerType->save();
            $networkServerType->amount = "0";
            $networkServerType->defaultServerType = false;
            $networkServerType->manualStart = false;
            $network->servertypes()->save($networkServerType);

            return Redirect::to('/')->with('open'.$network->id, 'successServerTypeAdd')->with('success', 'Added the server type '.$servertype->name.' to the network '.$network->name);
        }
    }

    public function deleteServerType(Network $network = null, $networkServerType = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        $networkServerType = $network->servertypes()->where("_id", "=", $networkServerType)->first();
        if ($networkServerType == null) {
            return Redirect::to('/')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $networkServerType->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successServerTypeDelete')->with('success', 'Deleted server type '.$networkServerType->servertype()->name.' from '.$network->name);
    }

    public function postBungeeType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $bungeetype = BungeeType::find($value);

            if ($bungeetype == null) {
                return false;
            }

            return true;
        }, 'Please select a valid bungee type');

        $validator = Validator::make(
            array('bungeetype'=>Input::get('bungeetype')),
            array('bungeetype'=>'required|checkType')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddBungeeType')->with('errorAddBungeeType'.$network->id, $validator->messages());
        } else {
            $bungeeType = BungeeType::find(Input::get('bungeetype'));
            $networkBungeeType = new NetworkBungeeType(array('network_id'=>$network->id, 'bungee_type_id'=>$bungeeType->id));

            Validator::extend('bungeetypeExists', function($attribute, $value, $parameters) use($network) {

                if ($network->bungeetypes()->where('bungee_type_id', '=', $value->bungee_type_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The bungee type '.$bungeeType->name.' is already added');

            $validator = Validator::make(
                array('bungeeType'=>$networkBungeeType),
                array('bungeeType'=>'bungeetypeExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorAddBungeeType')->with('errorAddBungeeType'.$network->id, $validator->messages());
            }

            $networkBungeeType->amount = "0";
            $networkBungeeType->addresses = array();
            $network->bungeetypes()->save($networkBungeeType);

            return Redirect::to('/')->with('open'.$network->id, 'successBungeeTypeAdd')->with('success', 'Added the bungee type '.$bungeeType->name.' to the network '.$network->name);
        }
    }

    public function putBungeeType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        foreach ($network->bungeetypes()->get() as $bungeeType) {
            $amount = Input::get($network->id.'amount'.$bungeeType->id);

            if ($bungeeType->amount == $amount) {
                continue;
            }

            if ($bungeeType->amount > $amount) {
                $toDelete = $bungeeType->amount - $amount;
                Log::info("Deleting ".$toDelete);
                foreach ($bungeeType->addresses()->get() as $address) {
                    if ($toDelete == 0) {
                        break;
                    }
                    $address->delete();
                    $toDelete -= 1;
                }
                /*$addresses = json_decode($bungeeType->addresses, true);
                Log::info("Before ".json_encode($addresses));
                for ($i = 0; $i < count($addresses); $i++) {
                    if ($toDelete == 0) {
                        break;
                    }
                    Log::info($addresses[$i]);
                    unset($addresses[$i]);
                    Log::info("After ".count($addresses));
                    $toDelete -= 1;
                }
                $addresses = array_values($addresses);
                Log::info("After ".count($addresses));
                $bungeeType->addresses = [];
                $bungeeType->addresses = $addresses;

                Log::info("Bungee Addrs ".$bungeeType->addresses);
                Log::info("Bungee Embeded Addrs ".$bungeeType->addresses()->get());*/
            } else if ($bungeeType->amount < $amount) {
                $toAdd = $amount - $bungeeType->amount;

                $found = array();
                foreach ($network->nodes()->get() as $node) {
                    $ram = $node->node()->ram;
                    foreach ($network->bungeetypes()->get() as $testType) {
                        foreach ($testType->addresses()->where('node_id', '=', $node->node()->id)->get() as $testType2) {
                            Log::info("Minus Ram ".$testType->bungeetype()->ram * $testType->amount);
                            $ram -= $testType->bungeetype()->ram * $testType->amount;
                        }
                    }

                    $ram -= $bungeeType->bungeetype()->ram;
                    Log::info("Ram ".$ram);

                    if ($ram < 0) {
                        continue;
                    }

                    foreach($node->node()->publicaddresses()->get() as $address) {
                        $addressTaken = false;

                        foreach ($network->bungeetypes()->get() as $testType) {
                            if ($testType->addresses()->where('node_public_address_id', '=', $address->id)->count() >= 1) {
                                $addressTaken = true;
                                break;
                            }
                        }

                        if ($addressTaken == false) {
                            $found[] = new NetworkBungeeTypeAddress(array("node_id" => $node->node()->id, "node_public_address_id" => $address->id));
                        }
                    }
                }

                //Log::info('Found addresses '.count($found). ' '.implode($found));

                if (count($found) < $toAdd) {
                    return Redirect::to('/')->with('open'.$network->id, 'errorUpdateBungeeType')->with('errorUpdateBungeeType'.$network->id, 'Not enough public addresses for '.$amount.' '.$bungeeType->bungeetype()->name.'(s)');
                }

                for ($i = 0; $i < $toAdd; $i++) {
                    $bungeeType->addresses()->save($found[$i]);
                }
            }

            $bungeeType->amount = $amount;
            $bungeeType->save();

            $network->save();
            //$bungeeType = $network->bungeetypes()->where('_id', '=', $bungeeType->id)->first();

            Log::info("Bungee Addrs2 ".$bungeeType->addresses);
            Log::info("Bungee Embeded Addrs2 ".$bungeeType->addresses()->get());
        }

        return Redirect::to('/')->with('open'.$network->id, 'successUpdateBungeeType')->with('success', 'Updated bungee types for the network '.$network->name);
    }

    public function deleteBungeeType(Network $network = null, $networkBungeeType = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        $networkBungeeType = $network->bungeetypes()->where("_id", "=", $networkBungeeType)->first();
        if ($networkBungeeType == null) {
            return Redirect::to('/')->with('error', 'Unknown bungee type Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $networkBungeeType->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successBungeeTypeDelete')->with('success', 'Deleted bungee type '.$networkBungeeType->bungeetype()->name.' from '.$network->name);
    }

    public function postForcedHost(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        $forcedhost = new NetworkForcedHost(array('host'=>Input::get('host')));

        Validator::extend('forcedHostExists', function($attribute, $value, $parameters) use ($network) {
            if ($network->forcedhosts()->where('host', '=', $value)->count() >= 1) {
                return false;
            }

            return true;
        }, 'Forced host is already added.');

        $validator = Validator::make(
            array('host'=>Input::get('host')),
            array('host'=>'required||min:3|max:100|forcedHostExists')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddForcedHost')->with('errorAddForcedHost'.$network->id, $validator->messages());
        }

        $network->forcedhosts()->save($forcedhost);

        return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('success', 'Added the forced host '.$forcedhost->host.' to the network '.$network->name);
    }

    public function putForcedHost(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        foreach ($network->forcedhosts()->get() as $forcedhost) {
            $servertype = Input::get($network->id.'servertype'.$forcedhost->id);

            $forcedhost->server_type_id = $servertype;

            if ($servertype == -1) {
                $forcedhost->server_type_id = null;
            } else {
                $forcedhost->server_type_id = $servertype;
            }

            $forcedhost->save();
        }

        return Redirect::to('/')->with('open'.$network->id, 'successUpdateForcedHost')->with('success', 'Updated forced hosts for the network '.$network->name);
    }

    public function deleteForcedHost(Network $network = null, $forcedhost = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        $forcedhost = $network->forcedhosts()->where("_id", "=", $forcedhost)->first();
        if ($forcedhost == null) {
            return Redirect::to('/')->with('error', 'Unknown forced host Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $forcedhost->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successServerTypeDelete')->with('success', 'Deleted forced host '.$forcedhost->host.' from '.$network->name);
    }

    public function postManualServerType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('manualServerTypeExists', function($attribute, $value, $parameters) use ($network) {
            if ($network->manualservertypes()->where('name', '=', $value)->count() >= 1) {
                return false;
            }

            return true;
        }, 'Forced host is already added.');

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'address'=>Input::get('address'),
                'port'=>Input::get('port')),
            array('name'=>'required|min:3|max:100|manualServerTypeExists',
                'address'=>'required',
                'port'=>'required|numeric')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
        }

        $manualServerType = new NetworkManualServerType(array('name'=>Input::get('name'), 'address'=>Input::get('address'), 'port'=>Input::get('port')));

        $network->manualservertypes()->save($manualServerType);

        return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('success', 'Added the manual server type '.$manualServerType->name.' to the network '.$network->name);
    }

    public function deleteManualServerType(Network $network = null, $manualServerType = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        $manualServerType = $network->manualservertypes()->where("_id", "=", $manualServerType)->first();
        if ($manualServerType == null) {
            return Redirect::to('/')->with('error', 'Unknown manual server type Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $manualServerType->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successServerTypeDelete')->with('success', 'Deleted manual server type '.$manualServerType->name.' from '.$network->name);
    }

    public function postNode(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('nodeExists', function($attribute, $value, $parameters) {
            $node = Node::find($value);

            if ($node == null) {
                return false;
            }

            return true;
        }, 'Please select a valid node');

        Validator::extend('otherNetworks', function($attribute, $value, $parameters) {
            foreach (Network::all() as $network) {
                if ($network->nodes()->where('node_id', '=', $value)->count() >= 1) {
                    return false;
                }
            }

            return true;
        }, 'Node is already added to another network.');

        $validator = Validator::make(
            array('node'=>Input::get('node')),
            array('node'=>'required|nodeExists|otherNetworks')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('errorAddNode'.$network->id, $validator->messages());
        } else {
            $node = Node::find(Input::get('node'));
            $networkNode = new NetworkNode(array('network_id'=>$network->id, 'node_id'=>$node->id));

            Validator::extend('nodeExists', function($attribute, $value, $parameters) use($network) {

                if ($network->nodes()->where('node_id', '=', $value->node_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The node '.$node->name.' is already added');


            $validator = Validator::make(
                array('node'=>$networkNode),
                array('node'=>'nodeExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('errorAddNode'.$network->id, $validator->messages());
            }

            //$networkNode->save();
            $network->nodes()->save($networkNode);

            return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('success', 'Added the node '.$node->name.' to the network '.$network->name);
        }
    }

    public function deleteNode(Network $network = null, $networkNode = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        $networkNode = $network->nodes()->where("_id", "=", $networkNode)->first();
        if ($networkNode == null) {
            return Redirect::to('/')->with('error', 'Unknown node Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $networkNode->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successServerTypeDelete')->with('success', 'Deleted node '.$networkNode->node()->name.' from '.$network->name);
    }

}