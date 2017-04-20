<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIotaToCustomization extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customizations', function(Blueprint $t){
			$t->string('iota',10);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customizations', function(Blueprint $t){
			$t->dropColumn('iota');
		});
	}

}
