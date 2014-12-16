<?php

class NodeController extends BaseController {

    public function getNodes() {
        if (Auth::user()->can('read_nodes') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the nodes page');
        }

        return View::make('nodes');
    }

    public function postNode() {
        if (Auth::user()->can('create_node') == false) {
            return Redirect::to('/nodes')->with('errorAdd', 'You do not have permissions to add nodes.');
        }

        $node = new Node;
        $node->name = Input::get('name');
        $node->privateAddress = Input::get('privateAddress');
        $node->ram = Input::get('ram');

        Validator::extend('checkip', function($attribute, $value, $params) {
            $split = explode('.', $value);
            if (count($split) != 4) {
                return false;
            }

            if (intval($split[0]) < 0 || intval($split[0]) > 255) {
                return false;
            }

            if (intval($split[1]) < 0 || intval($split[1]) > 255) {
                return false;
            }

            if (intval($split[2]) < 0 || intval($split[2]) > 255) {
                return false;
            }

            if (intval($split[3]) < 0 || intval($split[3]) > 255) {
                return false;
            }

            return true;
        });

        $validator = Validator::make(
            array('name'=>$node->name,
                'privateAddress'=>$node->privateAddress,
                'ram'=>$node->ram),
            array('name'=>'required|min:3|max:100|unique:nodes',
                'privateAddress'=>'required|unique:nodes|checkip',
                'ram'=>'required|Integer|Min:1024'),
            array('checkip'=>'Invalid IP address')
        );

        if ($validator->fails()) {
            return Redirect::to('/nodes')->with('errorAdd', $validator->messages());
        } else {

            $node->save();

            return Redirect::to('/nodes')->with('open'.$node->id, 'successAdd')->with('success', 'Created the node '.$node->name.' ('.$node->privateAddress.')');

        }
    }

    public function putNode(Node $node = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
        }

        if (Auth::user()->can('update_node') == false) {
            return Redirect::to('/nodes')->with('errorEdit', 'You do not have permissions to update nodes.');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'ram'=>Input::get('ram')),
            array('name'=>'required|min:3|max:100|unique:nodes,id,'.$node->id,
                'ram'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/nodes')->with('open'.$node->id, 'errorEdit')->with('errorEdit'.$node->id, $validator->messages());
        } else {
            $node->name = Input::get('name');
            $node->ram = Input::get('ram');

            $node->save();
            return Redirect::to('/nodes')->with('open'.$node->id, 'successEdit')->with('success', 'Updated the node '.$node->name.' ('.$node->address.')');

        }

    }

    public function deleteNode(Node $node = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
        }

        if (Auth::user()->can('delete_node') == false) {
            return Redirect::to('/nodes')->with('error', 'You do not have permissions to delete nodes');
        }

        $node->delete();

        return Redirect::to('/nodes')->with('success', 'Deleted node '.$node->name);
    }

    public function postPAddress(Node $node = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
        }

        if (Auth::user()->can('update_node') == false) {
            return Redirect::to('/nodes')->with('errorEdit', 'You do not have permissions to update nodes.');
        }

        Validator::extend('checkip', function($attribute, $value, $params) {
            $split = explode('.', $value);
            if (count($split) != 4) {
                return false;
            }

            if (intval($split[0]) < 0 || intval($split[0]) > 255) {
                return false;
            }

            if (intval($split[1]) < 0 || intval($split[1]) > 255) {
                return false;
            }

            if (intval($split[2]) < 0 || intval($split[2]) > 255) {
                return false;
            }

            if (intval($split[3]) < 0 || intval($split[3]) > 255) {
                return false;
            }

            return true;
        }, 'Invalid IP Address');

        $validator = Validator::make(
            array('publicAddress'=>Input::get('publicAddress')),
            array('publicAddress'=>'required|unique:node_public_addresses|checkip')
        );

        if ($validator->fails()) {
            return Redirect::to('/nodes')->with('open'.$node->id, 'errorIP')->with('errorIP'.$node->id, $validator->messages());
        } else {

            $nodePAddress = NodePublicAddress::firstOrNew(array('node_id'=>$node->id, 'publicAddress'=>Input::get('publicAddress')));
            $nodePAddress->publicAddress = Input::get('publicAddress');
            $nodePAddress->save();

            return Redirect::to('/nodes')->with('open'.$node->id, 'successAddIP')->with('success', 'Added the public address '.$nodePAddress->publicAddress.' to node '.$node->name);
        }
    }

    public function deletePAddress(Node $node = null, NodePublicAddress $address = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
        }

        if ($address == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown address Id');
        }


        if (Auth::user()->can('update_node') == false) {
            return Redirect::to('/nodes')->with('errorEdit', 'You do not have permissions to update nodes.');
        }

        $address->delete();

        return Redirect::to('/nodes')->with('open'.$node->id, 'successDeleteIP')->with('success', 'Deleted address '.$address->publicAddress.' from node '.$node->name);
    }

}