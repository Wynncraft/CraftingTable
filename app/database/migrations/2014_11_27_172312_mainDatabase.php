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


		// Create the plugins table
		Schema::create('plugins', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->enum('type', array('SERVER', 'BUNGEE'))->index();
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

		// Create the plugin versions table
		Schema::create('plugin_configs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('plugin_id')->unsigned()->index();
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->string('directory', 255);
			$table->timestamps();

			$table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
		});

		// Create the worlds table
		Schema::create('worlds', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->string('directory', 255);
			$table->timestamps();
		});

		// Create the world versions table
		Schema::create('world_versions', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('world_id')->unsigned()->index();
			$table->string('version', 100)->index();
			$table->string('description', 255)->nullable();
			$table->timestamps();

			$table->foreign('world_id')->references('id')->on('worlds')->onDelete('cascade');
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
			$table->integer('plugin_id')->unsigned()->index();
			$table->integer('pluginversion_id')->unsigned();
			$table->integer('pluginconfig_id')->unsigned()->nullable();
			$table->timestamps();

			$table->foreign('servertype_id')->references('id')->on('servertypes')->onDelete('cascade');
			$table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
			$table->foreign('pluginversion_id')->references('id')->on('plugin_versions')->onDelete('cascade');
			$table->foreign('pluginconfig_id')->references('id')->on('plugin_configs')->onDelete('cascade');
		});

		// Create the servertype/world relationship table
		Schema::create('servertype_worlds', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('servertype_id')->unsigned()->index();
			$table->integer('world_id')->unsigned()->unique()->index();
			$table->integer('worldversion_id')->unsigned()->index();
			$table->boolean('default')->index();
			$table->timestamps();

			$table->foreign('servertype_id')->references('id')->on('servertypes')->onDelete('cascade');
			$table->foreign('world_id')->references('id')->on('worlds')->onDelete('cascade');
			$table->foreign('worldversion_id')->references('id')->on('world_versions')->onDelete('cascade');
		});

		// Create the servertypes table
		Schema::create('bungeetypes', function($table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name', 100)->index();
			$table->string('description', 255)->nullable();
			$table->integer('ram');
			$table->timestamps();
		});

		// Create the bungee/plugin relationship table
		Schema::create('bungeetype_plugins', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('bungeetype_id')->unsigned()->index();
			$table->integer('plugin_id')->unsigned()->index();
			$table->integer('pluginversion_id')->unsigned();
			$table->integer('pluginconfig_id')->unsigned()->nullable();
			$table->timestamps();

			$table->foreign('bungeetype_id')->references('id')->on('bungeetypes')->onDelete('cascade');
			$table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
			$table->foreign('pluginversion_id')->references('id')->on('plugin_versions')->onDelete('cascade');
			$table->foreign('pluginconfig_id')->references('id')->on('plugin_configs')->onDelete('cascade');
		});

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
			$table->string('private_address', 15)->unique()->index();
			$table->integer('ram');
			$table->timestamps();
		});

		// Create the nodes public addresses table
		Schema::create('node_public_addresses', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('node_id')->unsigned()->index();
			$table->string('public_address', 15)->unique()->index();
			$table->timestamps();

			$table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
		});

		// Create the network/node relationship table
		Schema::create('network_nodes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('network_id')->unsigned()->index();
			$table->integer('node_id')->unsigned()->index();
			$table->integer('node_public_address_id')->unique()->unsigned()->nullable();
			$table->integer('bungee_type_id')->unsigned()->nullable();
			$table->timestamps();

			$table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
			$table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
			$table->foreign('node_public_address_id')->references('id')->on('node_public_addresses')->onDelete('cascade');
			$table->foreign('bungee_type_id')->references('id')->on('bungeetypes')->onDelete('cascade');
		});

		// Create the network/servertype relationship table
		Schema::create('network_servertypes', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('network_id')->unsigned()->index();
			$table->integer('server_type_id')->unsigned()->index();
			$table->integer('amount');
			$table->boolean('default');
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
		Schema::drop('network_servertypes');
		Schema::drop('network_nodes');
		Schema::drop('networks');

		Schema::drop('node_public_addresses');
		Schema::drop('nodes');

		Schema::drop('bungeetype_plugins');
		Schema::drop('bungeetypes');

		Schema::drop('servertype_plugins');
		Schema::drop('servertype_worlds');
		Schema::drop('servertypes');

		Schema::drop('plugin_versions');
		Schema::drop('plugins');

		Schema::drop('world_versions');
		Schema::drop('worlds');

	}

}
