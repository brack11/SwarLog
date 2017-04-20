<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBandsTable extends Migration {

	public function up()
	{
		Schema::create('bands', function(Blueprint $table) {
			$table->increments('id');
			$table->softDeletes();
			$table->string('name');
			$table->string('low');
			$table->string('high');
		});
	}

	public function down()
	{
		Schema::drop('bands');
	}
}