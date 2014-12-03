<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MainDatabase extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the networks table
		Schema::create('networks', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->timestamps();
		});

		// Create the nodes table
		Schema::create('nodes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('address', 15)->index();
			$table->integer('ram');
			$table->timestamps();
		});

		// Create the network/node relationship table
		Schema::create('network_node', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('network_id')->unsigned()->index();
			$table->integer('node_id')->unsigned()->index();
			$table->timestamps();

			$table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
			$table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
		});

		// Create the plugins table
		Schema::create('plugins', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->string('directory', 255);
			$table->timestamps();
		});

		// Create the plugin versions table
		Schema::create('plugin_versions', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('plugin_id')->unsigned()->index();
			$table->string('version', 100)->index();
			$table->string('description', 255)->nullable();
			$table->timestamps();

			$table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
		});

		// Create the servertypes table
		Schema::create('servertypes', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->integer('ram');
			$table->integer('players');
			$table->timestamps();
		});

		// Create the servertype/plugin relationship table
		Schema::create('servertype_plugins', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('servertype_id')->unsigned()->index();
			$table->integer('plugin_id')->unsigned()->unique()->index();
			$table->integer('pluginversion_id')->unsigned()->index();
			$table->timestamps();

			$table->foreign('servertype_id')->references('id')->on('servertypes')->onDelete('cascade');
			$table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
			$table->foreign('pluginversion_id')->references('id')->on('plugin_versions')->onDelete('cascade');
		});

		// Create the network/servertype relationship table
		Schema::create('network_servertype', function($table)
		{
			$table->engine = 'InnoDB';

			$table->integer('network_id')->unsigned()->index();
			$table->integer('server_type_id')->unsigned()->index();
			$table->integer('amount');
			$table->timestamps();

			$table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
			$table->foreign('server_type_id')->references('id')->on('servertypes')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('network_servertype');
		Schema::drop('network_node');
		Schema::drop('networks');
		Schema::drop('nodes');
		Schema::drop('servertype_plugins');
		Schema::drop('plugin_versions');
		Schema::drop('plugins');
		Schema::drop('servertypes');
	}

}
