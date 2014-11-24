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
        $role = new Toddish\Verify\Models\Role;
        return View::make('group')->with('role', $role);
    }

    private function groupPermissions() {
        $perms = array();

        $permInput = Input::all();

        foreach ($permInput as $perm => $value) {
            if (substr($perm, 0, 5) == 'PERM:') {
                if ($value == 'true') {
                    $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => substr($perm, 5, strlen($perm))));
                    $permission->save();
                    $perms[] = $permission->id;
                }
            }
        }

        return $perms;
    }

    public function deleteGroup(Toddish\Verify\Models\Role $role = null) {
        if ($role == null) {
            return Redirect::to('/groups')->with('error', 'Unknown group Id');
        }

        if ($role->name == Config::get('verify::super_admin')) {
            return Redirect::to('/groups')->with('error', 'Cannot delete Super Admin group.');
        }

        $role->delete();

        return Redirect::to('/groups')->with('success', 'Deleted group '.$role->name);
    }

    public function putGroup(Toddish\Verify\Models\Role $role = null) {
        if ($role == null) {
            return Redirect::to('/groups')->with('error', 'Unknown group Id');
        }

        if ($role->name == Config::get('verify::super_admin')) {
            return Redirect::to('/groups')->with('error', 'Cannot modify Super Admin group.');
        }

        $validator = Validator::make(
            array('name'=>$role->name),
            array('name'=>'required|unique:roles')
        );

        if ($role->name != Input::get('name') && $validator->fails()) {
            return View::make('group')->with('error', $validator->messages())->with('role', $role);
        } else {
            $role->name = Input::get('name');
            $role->description = Input::get('description');

            $role->save();

            $perms = $this->groupPermissions();

            $role->permissions()->sync($perms);

            return View::make('group')->with('success', 'Successfully updated the group '.$role->name)->with('role', $role);
        }
    }

    public function postGroup() {
        $role = Toddish\Verify\Models\Role::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$role->name),
            array('name'=>'required|unique:roles')
        );

        if ($validator->fails()) {
            return View::make('group')->with('error', $validator->messages())->with('role', $role);
        } else {
            $role->description = Input::get('description');
            $role->save();

            $perms = $this->groupPermissions();

            $role->permissions()->sync($perms);

            return Redirect::to('/groups')->with('success', 'Created group '.$role->name);
        }
    }

}
