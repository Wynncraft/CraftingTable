<?php

class NodeController extends BaseController {

    public function getNodes() {
        if (Auth::user()->can('read_nodes') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the nodes page');
        }

        return View::make('nodes');
    }

    public function postNode() {

    }

    public function putNode(Node $node = null) {
        if ($node == null) {
            return Redirect::to('/nodes')->with('error', 'Unknown node Id');
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