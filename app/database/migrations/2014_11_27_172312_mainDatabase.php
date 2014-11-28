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
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('network_node');
		Schema::drop('networks');
		Schema::drop('nodes');
	}

}
