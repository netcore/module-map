<?php

namespace Modules\Map\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;
use Netcore\Translator\Helpers\TransHelper;

class MapDatabaseSeeder extends Seeder
{
    /**
     * Application languages.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $languages;

    /**
     * MapsDatabaseSeeder constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedMenu();
        $this->seedSettings();
    }

    /**
     * Seed maps section menu entry.
     *
     * @return void
     */
    protected function seedMenu(): void
    {
        if (! method_exists(menu(), 'seedItems')) {
            $this->command->error('Menu seeding skipped - old admin module used in this project!');
            return;
        }

        menu()->seedItems([
            'leftAdminMenu' => [
                [
                    'icon'            => 'fa fa-map',
                    'type'            => 'route',
                    'active_resolver' => 'admin.maps.*',
                    'is_active'       => true,
                    'parameters'      => json_encode([]),

                    'name' => $this->languages->mapWithKeys(function ($language) {
                        return [$language->iso_code => 'Maps'];
                    }),

                    'value' => $this->languages->mapWithKeys(function ($language) {
                        return [$language->iso_code => 'map::maps.index'];
                    }),
                ],
            ],
        ]);
    }

    /**
     * Seed settings.
     *
     * @return void
     */
    protected function seedSettings(): void
    {
        collect([
            [
                'group' => 'maps',
                'name'  => 'Google Maps API key',
                'key'   => 'api_key',
                'type'  => 'text',
                'meta'  => [
                    'attributes' => [
                        'required' => 'required',
                    ],
                ],
            ],
        ])->each([Setting::class, 'create']);
    }
}
