<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Policy;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@prabhuinsurance.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $provinces = [
            ['province_name' => 'Province 1', 'code' => '1'],
            ['province_name' => 'Madhesh', 'code' => '2'],
            ['province_name' => 'Bagmati', 'code' => '3'],
            ['province_name' => 'Gandaki', 'code' => '4'],
            ['province_name' => 'Lumbini', 'code' => '5'],
            ['province_name' => 'Karnali', 'code' => '6'],
            ['province_name' => 'Sudurpaschim', 'code' => '7'],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['province_name' => $province['province_name']],
                ['code' => $province['code']]
            );
        }

        $provinceIds = Province::pluck('province_id', 'province_name');

        $districts = [
            ['district_id' => 1, 'province_name' => 'Province 1', 'district_name' => 'Ilam'],
            ['district_id' => 2, 'province_name' => 'Province 1', 'district_name' => 'Jhapa'],
            ['district_id' => 3, 'province_name' => 'Province 1', 'district_name' => 'Panchthar'],
            ['district_id' => 4, 'province_name' => 'Province 1', 'district_name' => 'Taplejung'],
            ['district_id' => 5, 'province_name' => 'Province 1', 'district_name' => 'Bhojpur'],
            ['district_id' => 6, 'province_name' => 'Province 1', 'district_name' => 'Dhankuta'],
            ['district_id' => 7, 'province_name' => 'Province 1', 'district_name' => 'Morang'],
            ['district_id' => 8, 'province_name' => 'Province 1', 'district_name' => 'Sankhuwasabha'],
            ['district_id' => 9, 'province_name' => 'Province 1', 'district_name' => 'Sunsari'],
            ['district_id' => 10, 'province_name' => 'Province 1', 'district_name' => 'Tehrathum'],
            ['district_id' => 11, 'province_name' => 'Province 1', 'district_name' => 'Khotang'],
            ['district_id' => 12, 'province_name' => 'Province 1', 'district_name' => 'Okhaldhunga'],
            ['district_id' => 15, 'province_name' => 'Province 1', 'district_name' => 'Solukhumbu'],
            ['district_id' => 16, 'province_name' => 'Province 1', 'district_name' => 'Udayapur'],
            ['district_id' => 17, 'province_name' => 'Madhesh', 'district_name' => 'Dhanusha'],
            ['district_id' => 19, 'province_name' => 'Madhesh', 'district_name' => 'Mahottari'],
            ['district_id' => 37, 'province_name' => 'Madhesh', 'district_name' => 'Bara'],
            ['district_id' => 13, 'province_name' => 'Madhesh', 'district_name' => 'Saptari'],
            ['district_id' => 14, 'province_name' => 'Madhesh', 'district_name' => 'Siraha'],
            ['district_id' => 21, 'province_name' => 'Madhesh', 'district_name' => 'Sarlahi'],
            ['district_id' => 40, 'province_name' => 'Madhesh', 'district_name' => 'Parsa'],
            ['district_id' => 41, 'province_name' => 'Madhesh', 'district_name' => 'Rautahat'],
            ['district_id' => 22, 'province_name' => 'Bagmati', 'district_name' => 'Sindhuli'],
            ['district_id' => 23, 'province_name' => 'Bagmati', 'district_name' => 'Bhaktapur'],
            ['district_id' => 24, 'province_name' => 'Bagmati', 'district_name' => 'Dhading'],
            ['district_id' => 25, 'province_name' => 'Bagmati', 'district_name' => 'Kathmandu'],
            ['district_id' => 26, 'province_name' => 'Bagmati', 'district_name' => 'Kavrepalanchok'],
            ['district_id' => 27, 'province_name' => 'Bagmati', 'district_name' => 'Lalitpur'],
            ['district_id' => 28, 'province_name' => 'Bagmati', 'district_name' => 'Nuwakot'],
            ['district_id' => 29, 'province_name' => 'Bagmati', 'district_name' => 'Rasuwa'],
            ['district_id' => 30, 'province_name' => 'Bagmati', 'district_name' => 'Sindhupalchok'],
            ['district_id' => 38, 'province_name' => 'Bagmati', 'district_name' => 'Chitwan'],
            ['district_id' => 39, 'province_name' => 'Bagmati', 'district_name' => 'Makwanpur'],
            ['district_id' => 20, 'province_name' => 'Bagmati', 'district_name' => 'Ramechhap'],
            ['district_id' => 18, 'province_name' => 'Bagmati', 'district_name' => 'Dolakha'],
            ['district_id' => 31, 'province_name' => 'Gandaki', 'district_name' => 'Gorkha'],
            ['district_id' => 32, 'province_name' => 'Gandaki', 'district_name' => 'Kaski'],
            ['district_id' => 33, 'province_name' => 'Gandaki', 'district_name' => 'Lamjung'],
            ['district_id' => 34, 'province_name' => 'Gandaki', 'district_name' => 'Manang'],
            ['district_id' => 35, 'province_name' => 'Gandaki', 'district_name' => 'Syangja'],
            ['district_id' => 36, 'province_name' => 'Gandaki', 'district_name' => 'Tanahun'],
            ['district_id' => 62, 'province_name' => 'Gandaki', 'district_name' => 'Baglung'],
            ['district_id' => 63, 'province_name' => 'Gandaki', 'district_name' => 'Mustang'],
            ['district_id' => 64, 'province_name' => 'Gandaki', 'district_name' => 'Myagdi'],
            ['district_id' => 65, 'province_name' => 'Gandaki', 'district_name' => 'Parbat'],
            ['district_id' => 69, 'province_name' => 'Gandaki', 'district_name' => 'Nawalpur'],
            ['district_id' => 70, 'province_name' => 'Lumbini', 'district_name' => 'Palpa'],
            ['district_id' => 71, 'province_name' => 'Lumbini', 'district_name' => 'Rupandehi'],
            ['district_id' => 66, 'province_name' => 'Lumbini', 'district_name' => 'Arghakhanchi'],
            ['district_id' => 67, 'province_name' => 'Lumbini', 'district_name' => 'Gulmi'],
            ['district_id' => 68, 'province_name' => 'Lumbini', 'district_name' => 'Kapilvastu'],
            ['district_id' => 77, 'province_name' => 'Lumbini', 'district_name' => 'Parasi'],
            ['district_id' => 47, 'province_name' => 'Lumbini', 'district_name' => 'Dang'],
            ['district_id' => 48, 'province_name' => 'Lumbini', 'district_name' => 'Pyuthan'],
            ['district_id' => 49, 'province_name' => 'Lumbini', 'district_name' => 'Rolpa'],
            ['district_id' => 50, 'province_name' => 'Lumbini', 'district_name' => 'Eastern Rukum'],
            ['district_id' => 52, 'province_name' => 'Lumbini', 'district_name' => 'Banke'],
            ['district_id' => 53, 'province_name' => 'Lumbini', 'district_name' => 'Bardiya'],
            ['district_id' => 54, 'province_name' => 'Karnali', 'district_name' => 'Dailekh'],
            ['district_id' => 55, 'province_name' => 'Karnali', 'district_name' => 'Jajarkot'],
            ['district_id' => 56, 'province_name' => 'Karnali', 'district_name' => 'Surkhet'],
            ['district_id' => 51, 'province_name' => 'Karnali', 'district_name' => 'Salyan'],
            ['district_id' => 42, 'province_name' => 'Karnali', 'district_name' => 'Dolpa'],
            ['district_id' => 43, 'province_name' => 'Karnali', 'district_name' => 'Humla'],
            ['district_id' => 44, 'province_name' => 'Karnali', 'district_name' => 'Jumla'],
            ['district_id' => 45, 'province_name' => 'Karnali', 'district_name' => 'Kalikot'],
            ['district_id' => 46, 'province_name' => 'Karnali', 'district_name' => 'Mugu'],
            ['district_id' => 76, 'province_name' => 'Karnali', 'district_name' => 'Western Rukum'],
            ['district_id' => 72, 'province_name' => 'Sudurpaschim', 'district_name' => 'Baitadi'],
            ['district_id' => 73, 'province_name' => 'Sudurpaschim', 'district_name' => 'Dadeldhura'],
            ['district_id' => 74, 'province_name' => 'Sudurpaschim', 'district_name' => 'Darchula'],
            ['district_id' => 75, 'province_name' => 'Sudurpaschim', 'district_name' => 'Kanchanpur'],
            ['district_id' => 57, 'province_name' => 'Sudurpaschim', 'district_name' => 'Achham'],
            ['district_id' => 58, 'province_name' => 'Sudurpaschim', 'district_name' => 'Bajhang'],
            ['district_id' => 59, 'province_name' => 'Sudurpaschim', 'district_name' => 'Bajura'],
            ['district_id' => 60, 'province_name' => 'Sudurpaschim', 'district_name' => 'Doti'],
            ['district_id' => 61, 'province_name' => 'Sudurpaschim', 'district_name' => 'Kailali'],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['district_name' => $district['district_name']],
                [
                    'district_id' => $district['district_id'],
                    'province_id' => $provinceIds[$district['province_name']],
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $district['district_name']), 0, 3)).str_pad((string) $district['district_id'], 2, '0', STR_PAD_LEFT),
                ]
            );
        }

        $policies = [
            'Property', 'Motor', 'Marine', 'Engineering', 'Aviation', 'Agriculture', 'Micro', 'Miscellaneous',
        ];

        foreach ($policies as $index => $policyName) {
            Policy::updateOrCreate(
                ['policy_name' => $policyName],
                [
                    'code' => 'PT'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'status' => 'active',
                ]
            );
        }

    }
}
