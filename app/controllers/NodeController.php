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
        $node->address = Input::get('address');
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
                'address'=>$node->address,
                'ram'=>$node->ram),
            array('name'=>'required|unique:nodes',
                'address'=>'required|unique:nodes|checkip',
                'ram'=>'required|Integer|Min:1024'),
            array('checkip'=>'Invalid IP address')
        );

        if ($validator->fails()) {
            return Redirect::to('/nodes')->with('errorAdd', $validator->messages());
        } else {

            $node->save();

            return Redirect::to('/nodes')->with('success', 'Created the node '.$node->name.' ('.$node->address.')');

        }
    }

    public function putNode(Node $node = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
        }

        if (Auth::user()->can('update_node') == false) {
            return Redirect::to('/nodes')->with('errorAdd', 'You do not have permissions to update nodes.');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'ram'=>Input::get('ram')),
            array('name'=>'required|unique:nodes,id,'.$node->id,
                'ram'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/nodes')->with('error'.$node->id, $validator->messages());
        } else {
            $node->name = Input::get('name');
            $node->ram = Input::get('ram');

            $node->save();
            return Redirect::to('/nodes')->with('success', 'Updated the node '.$node->name.' ('.$node->address.')');

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

}