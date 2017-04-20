<?php

class DatabaseSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();
		
        $this->call('ModesTableSeeder');
        $this->command->info('Modes table seeded!');

        $this->call('BandsTableSeeder');
        $this->command->info('Bands table seeded!');

	}
}