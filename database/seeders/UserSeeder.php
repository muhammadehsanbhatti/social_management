<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use DB;
use Exception;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();

        // check if table users is empty

            try{
                if(DB::table('users')->count() == 0){
                    DB::table('users')->insert([

                        [
                            'first_name' => 'Main',
                            'last_name' => 'Admin',
                            'email' => 'admin@gmail.com',
                            'password' => bcrypt('12345678@w'),
                            'role' => 'Admin',
                            'country' => 'Pakistan',
                            'phone_number' => '03119828878',
                            'dob' => 'Pakistan',
                            'profile_image' => 'app-assets/images/default-assets/default-profile-image.png',
                            'personal_identity' => 'app-assets/images/default-assets/default-image-image.png',
                            'identity_document' => 'app-assets/images/default-assets/default-image-image.png',
                            'dob' => '1998-09-02',
                            'description' => 'Description',
                            'address' => 'Address',
                            'user_status' => 'Verified',
                            'profile_completion' => 100,
                            'email_verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'first_name' => 'Sample',
                            'last_name' => 'User',
                            'email' => 'user@gmail.com',
                            'password' => bcrypt('12345678@w'),
                            'role' => 'User',
                            'country' => 'Pakistan',
                            'phone_number' => '03119828878',
                            'dob' => 'Pakistan',
                            'profile_image' => 'app-assets/images/default-assets/default-profile-image.png',
                            'personal_identity' => 'app-assets/images/default-assets/default-image-image.png',
                            'identity_document' => 'app-assets/images/default-assets/default-image-image.png',
                            'dob' => '1998-09-02',
                            'description' => 'Description',
                            'address' => 'Address',
                            'user_status' => 'Pending',
                            'profile_completion' => 100,
                            'email_verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'first_name' => 'Sample',
                            'last_name' => 'Employee',
                            'email' => 'employee@gmail.com',
                            'password' => bcrypt('12345678@w'),
                            'role' => 'Employee',
                            'country' => 'Pakistan',
                            'phone_number' => '03119828878',
                            'dob' => 'Pakistan',
                            'profile_image' => 'app-assets/images/default-assets/default-profile-image.png',
                            'personal_identity' => 'app-assets/images/default-assets/default-image-image.png',
                            'identity_document' => 'app-assets/images/default-assets/default-image-image.png',
                            'dob' => '1998-09-02',
                            'description' => 'Description',
                            'address' => 'Address',
                            'user_status' => 'Pending',
                            'profile_completion' => 100,
                            'email_verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);

                    $permissions = [
                        'edit-profile',
                        'change-password',
                        'user-list',
                        'user-detail',
                        'user-create',
                        'user-edit',
                        'user-delete',
                        'user-status',
                        'role-list',
                        'role-create',
                        'role-edit',
                        'role-delete',
                        'permission-list',
                        'permission-create',
                        'permission-edit',
                        'permission-delete',
                        'menu-list',
                        'menu-create',
                        'menu-edit',
                        'menu-delete',
                        'sub-menu-list',
                        'sub-menu-create',
                        'sub-menu-edit',
                        'sub-menu-delete',
                        'assign-permission',
                        'deposit-fund-list',
                        'deposit-fund-create',
                        'deposit-fund-edit',
                        'deposit-fund-delete',
                        'terms-condition-list',
                        'terms-condition-create',
                        'terms-condition-edit',
                        'terms-condition-delete',
                    ];

                    foreach ($permissions as $permission) {
                         Permission::create(['name' => $permission, 'guard_name' => 'web']);
                    }

                    $role = Role::where('name','Admin')->first();
                    $user = User::where('id', 1)->first();
                    $permissions = Permission::pluck('id','id')->all();
                    $role->syncPermissions($permissions);
                    $user->assignRole([$role->id]);

                    $role = Role::where('name','User')->first();
                    $user = User::where('id', 2)->first();
                    $permissions = Permission::whereIn('id',[1,2])->pluck('id','id')->all();
                    // $permissions = Permission::whereIn('id',[1,2,3,4,5])->pluck('id','id')->all();
                    $role->syncPermissions($permissions);
                    $user->assignRole([$role->id]);

                    $role = Role::where('name','Employee')->first();
                    $user = User::where('id', 3)->first();
                    $permissions = Permission::whereIn('id',[1,2])->pluck('id','id')->all();
                    $role->syncPermissions($permissions);
                    $user->assignRole([$role->id]);

                } else { echo "[User Table is not empty]\n"; }

            }catch(Exception $e) {
                echo $e->getMessage();
            }

    }
}
