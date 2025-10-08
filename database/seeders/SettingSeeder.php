<?php

namespace Database\Seeders;

use App\Enums\Setting\SettingFieldsEnum;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set default currency to Indonesian Rupiah
        settings()->set(SettingFieldsEnum::CURRENCY_SYMBOL->value, 'Rp');
        
        // Set decimal points
        settings()->set(SettingFieldsEnum::DECIMAL_POINT->value, 2);
        
        // Set default tax (0%)
        settings()->set(SettingFieldsEnum::TAX->value, 0);
        
        // Set default discount (0%)
        settings()->set(SettingFieldsEnum::DISCOUNT->value, 0);
        
        $this->command->info('Settings seeded successfully!');
    }
}
