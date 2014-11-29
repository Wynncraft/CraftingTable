<?php

class GroupController extends BaseController {

    public function getGroups() {
        return View::make('groups');
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

        if (Auth::user()->can('delete_group') == false) {
            return Redirect::to('/groups')->with('error', 'You do not have permissions to delete groups');
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

        if (Auth::user()->can('update_group') == false) {
            return Redirect::to('/groups')->with('error', 'You do not have permissions to update groups');
        }

        if ($role->name == Config::get('verify::super_admin')) {
            return Redirect::to('/groups')->with('error', 'Cannot modify Super Admin group.');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name')),
            array('name'=>'required|unique:roles')
        );

        if ($role->name != Input::get('name') && $validator->fails()) {
            return Redirect::to('/groups')->with('error'.$role->id, $validator->messages());
        } else {
            $role->name = Input::get('name');
            $role->description = Input::get('description');

            $role->save();

            $perms = $this->groupPermissions();

            $role->permissions()->sync($perms);

            return Redirect::to('/groups')->with('success', 'Successfully updated the group '.$role->name)->with('role', $role);
        }
    }

    public function postGroup() {

        if (Auth::user()->can('create_group') == false) {
            return Redirect::to('/groups')->with('error', 'You do not have permissions to create groups');
        }

        $role = Toddish\Verify\Models\Role::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$role->name),
            array('name'=>'required|unique:roles')
        );

        if ($validator->fails()) {
            return Redirect::to('/groups')->with('errorAdd', $validator->messages());
        } else {
            $role->description = Input::get('description');
            $role->save();

            return Redirect::to('/groups')->with('success', 'Created group '.$role->name);
        }
    }

}
