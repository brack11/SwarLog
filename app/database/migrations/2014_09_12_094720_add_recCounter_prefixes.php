<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRecCounterPrefixes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('prefixes', function(Blueprint $t){
			$t->integer('recCounter')->unique()->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('prefixes', function(Blueprint $t){
			$t->dropColumn('recCounter');
		});
	}

}
