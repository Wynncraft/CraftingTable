<?php

class GroupController extends BaseController {

    public function getGroups() {
        return View::make('groups');
    }

    public function getGroup(Toddish\Verify\Models\Role $role = null) {
        if ($role == null) {
            return Redirect::to('/groups')->with('error', 'Unknown group Id');
        }

        return View::make('group')->with('role', $role);
    }

    public function getAddGroup() {
        return View::make('group')->with('role', new Toddish\Verify\Models\Role);
    }

}
