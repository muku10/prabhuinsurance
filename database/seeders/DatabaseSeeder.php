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
                ['district_id' => $district['district_id']],
                [
                    'district_name' => $district['district_name'],
                    'province_id' => $provinceIds[$district['province_name']],
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $district['district_name']), 0, 3)).str_pad((string) $district['district_id'], 2, '0', STR_PAD_LEFT),
                ]
            );
        }

        $policies = [
            ['policy_id' => 1, 'parent_id' => null, 'policy_name' => 'Property', 'code' => 'PT01'],
            ['policy_id' => 2, 'parent_id' => null, 'policy_name' => 'Motor', 'code' => 'PT02'],
            ['policy_id' => 3, 'parent_id' => null, 'policy_name' => 'Marine', 'code' => 'PT03'],
            ['policy_id' => 4, 'parent_id' => null, 'policy_name' => 'Engineering', 'code' => 'PT04'],
            ['policy_id' => 5, 'parent_id' => null, 'policy_name' => 'Aviation', 'code' => 'PT05'],
            ['policy_id' => 6, 'parent_id' => null, 'policy_name' => 'Agriculture', 'code' => 'PT06'],
            ['policy_id' => 7, 'parent_id' => null, 'policy_name' => 'Micro', 'code' => 'PT07'],
            ['policy_id' => 8, 'parent_id' => null, 'policy_name' => 'Miscellaneous', 'code' => 'PT08'],
            ['policy_id' => 14, 'parent_id' => null, 'policy_name' => 'Imported Policy 14', 'code' => 'PT14'],
            ['policy_id' => 15, 'parent_id' => null, 'policy_name' => 'Imported Policy 15', 'code' => 'PT15'],
            ['policy_id' => 16, 'parent_id' => null, 'policy_name' => 'Imported Policy 16', 'code' => 'PT16'],
            ['policy_id' => 18, 'parent_id' => null, 'policy_name' => 'Imported Policy 18', 'code' => 'PT18'],
            ['policy_id' => 19, 'parent_id' => null, 'policy_name' => 'Imported Policy 19', 'code' => 'PT19'],
            ['policy_id' => 20, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 20', 'code' => 'SP20'],
            ['policy_id' => 21, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 21', 'code' => 'SP21'],
            ['policy_id' => 22, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 22', 'code' => 'SP22'],
            ['policy_id' => 23, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 23', 'code' => 'SP23'],
            ['policy_id' => 26, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 26', 'code' => 'SP26'],
            ['policy_id' => 27, 'parent_id' => 19, 'policy_name' => 'Imported Sub Policy 27', 'code' => 'SP27'],
            ['policy_id' => 28, 'parent_id' => null, 'policy_name' => 'Imported Policy 28', 'code' => 'PT28'],
            ['policy_id' => 29, 'parent_id' => 28, 'policy_name' => 'Imported Sub Policy 29', 'code' => 'SP29'],
            ['policy_id' => 30, 'parent_id' => 28, 'policy_name' => 'Imported Sub Policy 30', 'code' => 'SP30'],
            ['policy_id' => 31, 'parent_id' => 28, 'policy_name' => 'Imported Sub Policy 31', 'code' => 'SP31'],
            ['policy_id' => 44, 'parent_id' => 14, 'policy_name' => 'Imported Sub Policy 44', 'code' => 'SP44'],
            ['policy_id' => 46, 'parent_id' => null, 'policy_name' => 'Imported Policy 46', 'code' => 'PT46'],
            ['policy_id' => 47, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 47', 'code' => 'SP47'],
            ['policy_id' => 48, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 48', 'code' => 'SP48'],
            ['policy_id' => 49, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 49', 'code' => 'SP49'],
            ['policy_id' => 50, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 50', 'code' => 'SP50'],
            ['policy_id' => 51, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 51', 'code' => 'SP51'],
            ['policy_id' => 52, 'parent_id' => 46, 'policy_name' => 'Imported Sub Policy 52', 'code' => 'SP52'],
            ['policy_id' => 55, 'parent_id' => 14, 'policy_name' => 'Imported Sub Policy 55', 'code' => 'SP55'],
        ];

        foreach (array_filter($policies, fn (array $policy) => $policy['parent_id'] === null) as $policy) {
            Policy::updateOrCreate(
                ['policy_id' => $policy['policy_id']],
                [
                    'parent_id' => null,
                    'policy_name' => $policy['policy_name'],
                    'code' => $policy['code'],
                    'status' => 'active',
                ]
            );
        }

        foreach (array_filter($policies, fn (array $policy) => $policy['parent_id'] !== null) as $policy) {
            Policy::updateOrCreate(
                ['policy_id' => $policy['policy_id']],
                [
                    'parent_id' => $policy['parent_id'],
                    'policy_name' => $policy['policy_name'],
                    'code' => $policy['code'],
                    'status' => 'active',
                ]
            );
        }

        $branches = [
            ['branch_code' => 111, 'ext_branch_code' => 'KTM', 'branch_name' => 'Head Office', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Tinkune, Kathmandu', 'display_name' => 'Head Office'],
            ['branch_code' => 112, 'ext_branch_code' => 'BNP', 'branch_name' => 'Banepa', 'province_id' => 3, 'district_id' => 26, 'local_level' => 283, 'address' => 'Banepa, Kavrepalanchwok', 'display_name' => 'Banepa'],
            ['branch_code' => 113, 'ext_branch_code' => 'BRT', 'branch_name' => 'Biratnagar', 'province_id' => 1, 'district_id' => 7, 'local_level' => 72, 'address' => 'Biratnagar, Morang', 'display_name' => 'Biratnagar'],
            ['branch_code' => 114, 'ext_branch_code' => 'NGR', 'branch_name' => 'Narayangadh', 'province_id' => 3, 'district_id' => 38, 'local_level' => 396, 'address' => 'Narayangadh, chitwan', 'display_name' => 'Narayangadh'],
            ['branch_code' => 115, 'ext_branch_code' => 'BRJ', 'branch_name' => 'Birgunj', 'province_id' => 2, 'district_id' => 40, 'local_level' => 416, 'address' => 'Birgunj, Parsa', 'display_name' => 'Birgunj'],
            ['branch_code' => 116, 'ext_branch_code' => 'PKR', 'branch_name' => 'Pokhara', 'province_id' => 4, 'district_id' => 32, 'local_level' => 342, 'address' => 'Pokhara, Kaski', 'display_name' => 'Pokhara'],
            ['branch_code' => 117, 'ext_branch_code' => 'BUT', 'branch_name' => 'Butwal', 'province_id' => 5, 'district_id' => 71, 'local_level' => 691, 'address' => 'Butwal, Rupandehi', 'display_name' => 'Butwal'],
            ['branch_code' => 118, 'ext_branch_code' => 'NPJ', 'branch_name' => 'Nepalgunj', 'province_id' => 5, 'district_id' => 52, 'local_level' => 525, 'address' => 'Nepalgunj, Banke', 'display_name' => 'Nepalgunj'],
            ['branch_code' => 119, 'ext_branch_code' => 'HTD', 'branch_name' => 'Hetauda', 'province_id' => 3, 'district_id' => 39, 'local_level' => 405, 'address' => 'Hetauda', 'display_name' => 'Hetauda'],
            ['branch_code' => 120, 'ext_branch_code' => 'ITR', 'branch_name' => 'Itahari', 'province_id' => 1, 'district_id' => 9, 'local_level' => 91, 'address' => 'Itahari, Sunsari', 'display_name' => 'Itahari'],
            ['branch_code' => 121, 'ext_branch_code' => 'DNG', 'branch_name' => 'Dang', 'province_id' => 5, 'district_id' => 47, 'local_level' => 480, 'address' => 'Tulsipur, Dang', 'display_name' => 'Dang'],
            ['branch_code' => 122, 'ext_branch_code' => 'JPR', 'branch_name' => 'Janakpur', 'province_id' => 2, 'district_id' => 17, 'local_level' => 187, 'address' => 'Shiva Chwok, Janakpurdham', 'display_name' => 'Janakpur'],
            ['branch_code' => 123, 'ext_branch_code' => 'DHG', 'branch_name' => 'Dhangadhi', 'province_id' => 7, 'district_id' => 61, 'local_level' => 615, 'address' => 'Chatakpur', 'display_name' => 'Dhangadhi'],
            ['branch_code' => 124, 'ext_branch_code' => 'MST', 'branch_name' => 'Mustang', 'province_id' => 4, 'district_id' => 63, 'local_level' => 627, 'address' => 'Jomsom, Mustang', 'display_name' => 'Mustang'],
            ['branch_code' => 125, 'ext_branch_code' => 'MYG', 'branch_name' => 'Myagdi', 'province_id' => 4, 'district_id' => 64, 'local_level' => 636, 'address' => 'Beni, Myagdi', 'display_name' => 'Myagdi'],
            ['branch_code' => 126, 'ext_branch_code' => 'BGL', 'branch_name' => 'Baglung', 'province_id' => 4, 'district_id' => 62, 'local_level' => 616, 'address' => 'Baglung', 'display_name' => 'Baglung'],
            ['branch_code' => 127, 'ext_branch_code' => 'GLM', 'branch_name' => 'Gulmi', 'province_id' => 5, 'district_id' => 67, 'local_level' => 658, 'address' => 'Tamghas, Gulmi', 'display_name' => 'Gulmi'],
            ['branch_code' => 128, 'ext_branch_code' => 'SYN', 'branch_name' => 'Syangja', 'province_id' => 4, 'district_id' => 35, 'local_level' => 364, 'address' => 'Waling, Syangja', 'display_name' => 'Syangja'],
            ['branch_code' => 129, 'ext_branch_code' => 'LHN', 'branch_name' => 'Lahan', 'province_id' => 2, 'district_id' => 14, 'local_level' => 140, 'address' => 'Lahan, Siraha', 'display_name' => 'Lahan'],
            ['branch_code' => 130, 'ext_branch_code' => 'NRD', 'branch_name' => 'Newroad', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Khichapokhari, Newroad, Kathmandu', 'display_name' => 'Newroad'],
            ['branch_code' => 131, 'ext_branch_code' => 'DLK', 'branch_name' => 'Dolakha', 'province_id' => 3, 'district_id' => 18, 'local_level' => 199, 'address' => 'Charikot, Dolakha', 'display_name' => 'Dolakha'],
            ['branch_code' => 132, 'ext_branch_code' => 'PTN', 'branch_name' => 'Patan', 'province_id' => 3, 'district_id' => 27, 'local_level' => 294, 'address' => 'Kumaripati, Lalitpur', 'display_name' => 'Patan'],
            ['branch_code' => 133, 'ext_branch_code' => 'BTM', 'branch_name' => 'Birtamod', 'province_id' => 1, 'district_id' => 1, 'local_level' => 22, 'address' => 'Birtamod, Jhapa', 'display_name' => 'Birtamod'],
            ['branch_code' => 134, 'ext_branch_code' => 'CBL', 'branch_name' => 'Chabahil', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Chabahil, Kathmandu', 'display_name' => 'Chabahil'],
            ['branch_code' => 135, 'ext_branch_code' => 'GNB', 'branch_name' => 'Gongabu', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Gongabu, Samakhushi, Kathmandu', 'display_name' => 'Gongabu'],
            ['branch_code' => 136, 'ext_branch_code' => 'DRN', 'branch_name' => 'Dharan', 'province_id' => 1, 'district_id' => 9, 'local_level' => 86, 'address' => 'Dharan, Sunsari', 'display_name' => 'Dharan'],
            ['branch_code' => 137, 'ext_branch_code' => 'SKT', 'branch_name' => 'Surkhet', 'province_id' => 6, 'district_id' => 56, 'local_level' => 559, 'address' => 'Jumla Road, Surkhet', 'display_name' => 'Surkhet'],
            ['branch_code' => 138, 'ext_branch_code' => 'BKT', 'branch_name' => 'Bhaktapur', 'province_id' => 3, 'district_id' => 23, 'local_level' => 253, 'address' => 'Suryabinayak, Bhaktapur', 'display_name' => 'Bhaktapur'],
            ['branch_code' => 139, 'ext_branch_code' => 'DHN', 'branch_name' => 'Dhading', 'province_id' => 3, 'district_id' => 24, 'local_level' => 261, 'address' => 'Dhading Beshi, Dhading', 'display_name' => 'Dhading'],
            ['branch_code' => 140, 'ext_branch_code' => 'BHR', 'branch_name' => 'Bhairahawa', 'province_id' => 5, 'district_id' => 71, 'local_level' => 700, 'address' => 'Bhairahawa', 'display_name' => 'Bhairahawa'],
            ['branch_code' => 141, 'ext_branch_code' => 'KTP', 'branch_name' => 'Kirtipur', 'province_id' => 3, 'district_id' => 25, 'local_level' => 277, 'address' => 'Kirtipur', 'display_name' => 'Kirtipur'],
            ['branch_code' => 142, 'ext_branch_code' => 'NKT', 'branch_name' => 'Nuwakot', 'province_id' => 3, 'district_id' => 28, 'local_level' => 302, 'address' => 'Battar', 'display_name' => 'Nuwakot'],
            ['branch_code' => 143, 'ext_branch_code' => 'MLG', 'branch_name' => 'Malangwa', 'province_id' => 2, 'district_id' => 21, 'local_level' => 242, 'address' => 'Malangwa, Sarlahi', 'display_name' => 'Malangwa'],
            ['branch_code' => 144, 'ext_branch_code' => 'DML', 'branch_name' => 'Damauli', 'province_id' => 4, 'district_id' => 36, 'local_level' => 368, 'address' => 'Damauli, Tanahu', 'display_name' => 'Damauli'],
            ['branch_code' => 145, 'ext_branch_code' => 'LMG', 'branch_name' => 'Lamjung', 'province_id' => 4, 'district_id' => 33, 'local_level' => 348, 'address' => 'Besi Sahar, Lamjung', 'display_name' => 'Lamjung'],
            ['branch_code' => 146, 'ext_branch_code' => 'PRB', 'branch_name' => 'Parbat', 'province_id' => 4, 'district_id' => 65, 'local_level' => 639, 'address' => 'Kusma, Parbat', 'display_name' => 'Parbat'],
            ['branch_code' => 147, 'ext_branch_code' => 'KTR', 'branch_name' => 'Katari', 'province_id' => 1, 'district_id' => 16, 'local_level' => 171, 'address' => 'Katari, Udaypur', 'display_name' => 'Katari'],
            ['branch_code' => 148, 'ext_branch_code' => 'GRK', 'branch_name' => 'Gorkha', 'province_id' => 4, 'district_id' => 31, 'local_level' => 336, 'address' => 'Gorkha', 'display_name' => 'Gorkha'],
            ['branch_code' => 149, 'ext_branch_code' => 'GHT', 'branch_name' => 'Gaighat', 'province_id' => 1, 'district_id' => 16, 'local_level' => 167, 'address' => 'Gaighat, Udaypur', 'display_name' => 'Gaighat'],
            ['branch_code' => 150, 'ext_branch_code' => 'BBS', 'branch_name' => 'Bardibas', 'province_id' => 2, 'district_id' => 19, 'local_level' => 200, 'address' => 'Bardibas, Mahottari', 'display_name' => 'Bardibas'],
            ['branch_code' => 151, 'ext_branch_code' => 'KST', 'branch_name' => 'Kawasoti', 'province_id' => 4, 'district_id' => 69, 'local_level' => 677, 'address' => 'Kawasoti, Nawalpasari', 'display_name' => 'Kawasoti'],
            ['branch_code' => 152, 'ext_branch_code' => 'DMK', 'branch_name' => 'Damak', 'province_id' => 1, 'district_id' => 1, 'local_level' => 17, 'address' => 'Damak, Jhapa', 'display_name' => 'Damak'],
            ['branch_code' => 153, 'ext_branch_code' => 'SDL', 'branch_name' => 'Sindhuli', 'province_id' => 3, 'district_id' => 22, 'local_level' => 247, 'address' => 'Sindhuli Bazar, Sindhuli', 'display_name' => 'Sindhuli'],
            ['branch_code' => 154, 'ext_branch_code' => 'BPR', 'branch_name' => 'Bharatpur Yatayat', 'province_id' => 3, 'district_id' => 38, 'local_level' => 396, 'address' => 'Bharatpur, Chitwan', 'display_name' => 'Bharatpur Yatayat'],
            ['branch_code' => 155, 'ext_branch_code' => 'JPT', 'branch_name' => 'Jorpati', 'province_id' => 3, 'district_id' => 25, 'local_level' => 271, 'address' => 'Jorpati, Kathmandu', 'display_name' => 'Jorpati'],
            ['branch_code' => 156, 'ext_branch_code' => 'BHY', 'branch_name' => 'Bhairahawa Yatayat', 'province_id' => 5, 'district_id' => 71, 'local_level' => 700, 'address' => 'Bhairahawa, Rupandehi', 'display_name' => 'Bhairahawa Yatayat'],
            ['branch_code' => 157, 'ext_branch_code' => 'SWY', 'branch_name' => 'Swoyambhu Yatayat', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Swoyambhu Kathmandu', 'display_name' => 'Swoyambhu Yatayat'],
            ['branch_code' => 158, 'ext_branch_code' => 'HGD', 'branch_name' => 'Hattigauda', 'province_id' => 3, 'district_id' => 25, 'local_level' => 272, 'address' => 'Hattigauda, Kathmandu', 'display_name' => 'Hattigauda'],
            ['branch_code' => 159, 'ext_branch_code' => 'AMC', 'branch_name' => 'Amarsingh Chwok', 'province_id' => 4, 'district_id' => 32, 'local_level' => 342, 'address' => 'Amarsingh Chowk, Pokhara, Kaski', 'display_name' => 'Amarsingh Chwok'],
            ['branch_code' => 160, 'ext_branch_code' => 'GHI', 'branch_name' => 'Ghorahi', 'province_id' => 5, 'district_id' => 47, 'local_level' => 479, 'address' => 'Ghorahi, Dang', 'display_name' => 'Ghorahi'],
            ['branch_code' => 161, 'ext_branch_code' => 'KLK', 'branch_name' => 'Kalanki', 'province_id' => 3, 'district_id' => 25, 'local_level' => 276, 'address' => 'Kalanki, Kathmandu', 'display_name' => 'Kalanki'],
            ['branch_code' => 162, 'ext_branch_code' => 'RBJ', 'branch_name' => 'Rajbiraj', 'province_id' => 2, 'district_id' => 13, 'local_level' => 134, 'address' => 'Rajbiraj, Saptari', 'display_name' => 'Rajbiraj'],
            ['branch_code' => 164, 'ext_branch_code' => 'CGP', 'branch_name' => 'Chandranigahapur', 'province_id' => 2, 'district_id' => 41, 'local_level' => 431, 'address' => 'Chandranigahapur, Rautahat', 'display_name' => 'Chandranigahapur'],
            ['branch_code' => 165, 'ext_branch_code' => 'MTL', 'branch_name' => 'Manthali', 'province_id' => 3, 'district_id' => 20, 'local_level' => 219, 'address' => 'Manthali Bazar, Ramechhap', 'display_name' => 'Manthali'],
            ['branch_code' => 167, 'ext_branch_code' => 'MGR', 'branch_name' => 'Mahendranagar', 'province_id' => 7, 'district_id' => 75, 'local_level' => 735, 'address' => 'Chauraha, Mahendranagar', 'display_name' => 'Mahendranagar'],
            ['branch_code' => 168, 'ext_branch_code' => 'KVT', 'branch_name' => 'Kakarvitta', 'province_id' => 1, 'district_id' => 1, 'local_level' => 11, 'address' => 'Mechinagar Municipality -06, Jhapa', 'display_name' => 'Kakarvitta'],
            ['branch_code' => 169, 'ext_branch_code' => 'KHT', 'branch_name' => 'Khurkot', 'province_id' => 3, 'district_id' => 22, 'local_level' => 246, 'address' => 'Khurkot, Sindhuli', 'display_name' => 'Khurkot'],
        ];

        $branchCodes = array_column($branches, 'branch_code');

        DB::table('branch_network')->whereNotIn('branch_code', $branchCodes)->delete();

        foreach ($branches as $branch) {
            DB::table('branch_network')->updateOrInsert(
                ['branch_code' => $branch['branch_code']],
                array_merge($branch, [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
