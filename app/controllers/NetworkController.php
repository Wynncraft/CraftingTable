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
            array('name'=>'required|min:3|max:100|unique:networks,name,'.$network->id,
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
            array('servertype'=>Input::get('servertype'),
                'amount'=>Input::get('amount')),
            array('servertype'=>'required|checkType',
                'amount'=>'required|Integer|Min:1')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
        } else {
            $servertype = ServerType::find(Input::get('servertype'));
            $defaultServerType = null;
            $networkServerType = new NetworkServerType(array('network_id'=>$network->id, 'server_type_id'=>$servertype->id));

            Validator::extend('servertypeExists', function($attribute, $value, $parameters) use($network) {

                if ($network->servertypes()->where('server_type_id', '=', $value->server_type_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The server type '.$servertype->name.' is already added');

            if ($network->defaultServerType() != null) {
                $defaultServerType = $network->defaultServerType()->servertype()->name;
            }

            Validator::extend('typeDefault', function($attribute, $value, $parameters) {

                if (Input::has('default') == false) {
                    return true;
                }

                if ($value->defaultServerType() != null) {
                    return false;
                }

                return true;
            }, 'There is already a default server type '.$defaultServerType);

            $validator = Validator::make(
                array('serverType'=>$networkServerType,
                    'networkDefaultServerType'=>$network),
                array('serverType'=>'servertypeExists',
                    'networkDefaultServerType'=>'typeDefault')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
            }

            $networkServerType->amount = Input::get('amount');

            if (Input::has('default') == true) {
                $networkServerType->defaultServerType = true;
            } else {
                $networkServerType->defaultServerType = false;
            }

            if (Input::has('manualStart') == true) {
                $networkServerType->manualStart = true;
            } else {
                $networkServerType->manualStart = false;
            }

            //$networkServerType->save();
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

    public function postNode(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $node = Node::find($value);

            if ($node == null) {
                return false;
            }

            return true;
        }, 'Please select a valid node');

        $validator = Validator::make(
            array('node'=>Input::get('node')),
            array('node'=>'required|checkType')
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

            if (Input::get('bungeetype') != -1) {
                $bungeetype = BungeeType::find(Input::get('bungeetype'))->first();

                Validator::extend('checkRam', function($attribute, $value, $parameters) use($node) {

                    if ($node->ram < $value->ram) {
                        return false;
                    }

                    return true;
                }, 'Not enough ram on the node '.$node->name.' to have the bungee type '.$bungeetype->name);

                $validator = Validator::make(
                    array('bungeetype'=>$bungeetype),
                    array('bungeetype'=>'required|checkRam'),
                    array('required'=>'Unknown bungee type id')
                );
                Validator::getPresenceVerifier()->setConnection("mongodb");

                if ($validator->fails()) {
                    return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('errorAddNode'.$network->id, $validator->messages());
                }

                $networkNode->bungee_type_id = $bungeetype->id;

                $nodePublicAddress = null;

                if ($node->publicaddresses()->count() > 0) {
                    Log::info("Has addresses");
                    $usedAddressIds = array();
                    foreach (Network::all() as $testNetwork) {
                        Log::info("loop network ".$testNetwork->name);
                        $testNetworkNode = $testNetwork->nodes()->where("node_id", "=", $node->id)->first();
                        if ($testNetworkNode != null) {
                            Log::info("loop node ".$testNetworkNode->node()->name);
                            if ($testNetworkNode->node_public_address_id != null) {
                                Log::info("Adding address ".$testNetworkNode->node_public_address_id);
                                $usedAddressIds[] = $testNetworkNode->node_public_address_id;
                            }
                        }
                    }
                    foreach ($node->publicaddresses()->all() as $address) {
                        Log::info("Loop node address ".$address);
                        if (in_array($address->id, $usedAddressIds) == false) {
                            Log::info("Found free ".$address);
                            $nodePublicAddress = $address;
                            break;
                        }
                    }
                }

                $validator = Validator::make(
                    array('address'=>$nodePublicAddress),
                    array('address'=>'required'),
                    array('required'=>'No public address available on node '.$node->name)
                );
                Validator::getPresenceVerifier()->setConnection("mongodb");

                if ($validator->fails()) {
                    return Redirect::to('/')->with('open'.$network->id, 'errorAddNode')->with('errorAddNode'.$network->id, $validator->messages());
                } else {
                    $networkNode->node_public_address_id = $nodePublicAddress->id;
                }
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