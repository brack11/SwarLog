<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrefixesTable extends Migration {

	public function up()
	{
		Schema::create('prefixes', function(Blueprint $table) {
			$table->increments('id');
			$table->softDeletes();
			$table->string('InternalUse', 7);
			$table->string('lon');
			$table->string('lat');
			$table->string('territory');
			$table->string('prefix');
			$table->string('cq');
			$table->string('itu');
			$table->string('continent');
			$table->string('tz');
			$table->string('adif');
			$table->string('province');
			$table->text('mask');
		});
	}

	public function down()
	{
		Schema::drop('prefixes');
	}
}