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

    private function groupPermissions($role) {
        $perms = array();

        $perm = 'create_user';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'read_user';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'update_user';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'delete_user';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }

        $perm = 'create_group';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'read_group';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'update_group';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'delete_group';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }

        $perm = 'create_network';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'read_network';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'update_network';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }
        $perm = 'delete_network';
        if (Input::get($perm) == "true") {
            $permission = Toddish\Verify\Models\Permission::firstOrNew(array('name' => $perm));
            $permission->save();
            $perms[] = $permission->id;
        }

        return $perms;
    }

    public function putGroup(Toddish\Verify\Models\Role $role = null) {

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

            $perms = $this->groupPermissions($role);

            $role->permissions()->sync($perms);

            return View::make('group')->with('success', 'Successfully saved the group')->with('role', $role);
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

            $perms = $this->groupPermissions($role);

            $role->permissions()->sync($perms);

            return Redirect::to('/groups')->with('success', 'Created group '.$role->name);
        }
    }

}
