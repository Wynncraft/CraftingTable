<?php

use Illuminate\Database\Migrations\Migration;

class Demo extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = new Toddish\Verify\Models\Role;
        $role->name = Config::get('verify::super_admin');
        $role->description = 'Master Administrator Group';
        $role->save();

        $user = new Toddish\Verify\Models\User;
        $user->username = 'demo';
        $user->email = 'demo@minestack.io';
        $user->password = 'demo';
        $user->verified = 1;
        $user->disabled = 1;

        $user->save();

        $user->roles()->sync(array($role->id));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $user = User::find(1);
        $user->delete();

        $role = Toddish\Verify\Models\Role::find(1);
        $role->delete();
    }

}
