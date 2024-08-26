<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
// use Laravel\Sanctum\HasApiTokens;
use Carbon;
use Laravel\Passport\HasApiTokens;
use DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /*
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    */

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // public function Role()
    // {
    //     return $this->belongsTo(Role::class, 'role')
    //         ->select(['id', 'name']);
    // }
    // public function getUserStatusAttribute($value)
    // {
    //     $status ='';
    //     if($value == 1){
    //         $status = 'Active';
    //     }
    //     else if($value == 2){
    //         $status = 'Block';
    //     }
    //     return $status;
    // }

    public static function getUser($posted_data = array())
    {
        $query = User::latest();

        // if (!isset($posted_data['comma_separated_ids'])) {
        //     $query = $query->with('Role');
        // }

        if (isset($posted_data['id'])) {
            $query = $query->where('users.id', $posted_data['id']);
            $posted_data['detail'] = true;
        }
        if (isset($posted_data['users_in'])) {
            $query = $query->whereIn('users.id', $posted_data['users_in']);
        }
        if (isset($posted_data['users_not_in'])) {
            $query = $query->whereNotIn('users.id', $posted_data['users_not_in']);
        }
        if (isset($posted_data['email'])) {
            $query = $query->where('users.email', $posted_data['email']);
        }
        if (isset($posted_data['first_name'])) {
            $query = $query->where('users.first_name', 'like', '%' . $posted_data['first_name'] . '%');
        }
        if (isset($posted_data['last_name'])) {
            $query = $query->where('users.last_name', 'like', '%' . $posted_data['last_name'] . '%');
        }
        if (isset($posted_data['full_name'])) {
            $query = $query->where('users.full_name', 'like', '%' . $posted_data['full_name'] . '%');
        }
        if (isset($posted_data['roles'])) {
            $query = $query->whereHas("roles", function($qry) use ($posted_data) {
                        $qry->where("name", $posted_data['roles']);
                    });
        }
	    if (isset($posted_data['phone_number'])) {
            $query = $query->where('users.phone_number', $posted_data['phone_number']);
        }
	    if (isset($posted_data['country'])) {
            $query = $query->where('users.country', $posted_data['country']);
        }
	    if (isset($posted_data['profile_completion'])) {
            $query = $query->where('users.profile_completion', $posted_data['profile_completion']);
        }
	    if (isset($posted_data['address'])) {
            $query = $query->where('users.address', $posted_data['address']);
        }
	    if (isset($posted_data['personal_identity'])) {
            $query = $query->where('users.personal_identity', $posted_data['personal_identity']);
        }
	    if (isset($posted_data['role'])) {
            $query = $query->where('users.role', $posted_data['role']);
        }

	    if (isset($posted_data['role_in'])) {
            $query = $query->whereIn('users.role', $posted_data['role_in']);
        }
        if (isset($posted_data['user_status'])) {
            $query = $query->where('users.user_status', $posted_data['user_status']);
        }

        // if (isset($posted_data['verified_users'])) {
        //     $query = $query->where('users.user_status', $posted_data['verified_users']);
        // }
        if (isset($posted_data['last_seen'])) {
            $query = $query->where('users.last_seen', $posted_data['last_seen']);
        }
        if (isset($posted_data['time_spent'])) {
            $query = $query->where('users.time_spent', $posted_data['time_spent']);
        }
        if (isset($posted_data['theme_mode'])) {
            $query = $query->where('users.theme_mode', $posted_data['theme_mode']);
        }
        if (isset($posted_data['description'])) {
            $query = $query->where('users.description', $posted_data['description']);
        }
        if (isset($posted_data['login_having_thirty_minutes'])) {
            $query = $query->where('users.last_seen','<=', $posted_data['login_having_thirty_minutes']);
        }
        if (isset($posted_data['comma_separated_ids'])) {
            $query = $query->selectRaw("GROUP_CONCAT(id) as ids");
            $posted_data['detail'] = true;
        }
        if (isset($posted_data['search'])) {
            $searchTerm = $posted_data['search'];

            $query = $query->where(function ($q) use ($searchTerm) {
                $q->where('users.first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('users.last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('users.email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('users.phone_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // if (isset($posted_data['created_at'])) {
        //     $query = $query->where('created_at', $posted_data['created_at']);
        // }

        $query->getQuery()->orders = null;
        if (isset($posted_data['orderBy_name']) && isset($posted_data['orderBy_value'])) {
            $query->orderBy($posted_data['orderBy_name'], $posted_data['orderBy_value']);
        } else {
            $query->orderBy('users.id', 'DESC');
        }


        if (isset($posted_data['paginate'])) {
            $result = $query->paginate($posted_data['paginate']);
        } else {
            if (isset($posted_data['detail'])) {
                $result = $query->first();
            } else if (isset($posted_data['count'])) {
                $result = $query->count();
            } else if (isset($posted_data['array'])) {
                $result = $query->get()->ToArray();
            } else {
                $result = $query->get();
            }
        }

        if(isset($posted_data['printsql'])){
            $result = $query->toSql();
            echo '<pre>';
            print_r($result);
            print_r($posted_data);
            exit;
        }
        return $result;
    }

    public static function saveUpdateUser($posted_data = array(), $where_posted_data = array())
    {
        if (isset($posted_data['update_id'])) {
            $data = User::find($posted_data['update_id']);
        } else {
            $data = new User;
        }

        if(isset($where_posted_data) && count($where_posted_data)>0){
            $is_updated = false;
            if (isset($where_posted_data['user_status'])) {
                $is_updated = true;
                $data = $data->where('user_status', $where_posted_data['user_status']);
            }

            if($is_updated){
                return $data->update($posted_data);
            }else{
                return false;
            }
        }


        if (isset($posted_data['first_name'])) {
            $data->first_name = $posted_data['first_name'];
        }
        if (isset($posted_data['last_name'])) {
            $data->last_name = $posted_data['last_name'];
        }
        if (isset($posted_data['full_name'])) {
            $data->full_name = $posted_data['full_name'];
        }
        if (isset($posted_data['email'])) {
            $data->email = $posted_data['email'];
        }
        if (isset($posted_data['password'])) {
            $data->password = Hash::make($posted_data['password']);
        }
        if (isset($posted_data['user_type'])) {
            $data->user_type = $posted_data['user_type'];
        }
        if (isset($posted_data['dob'])) {
            $data->dob = $posted_data['dob'];
        }
        if (isset($posted_data['location'])) {
            $data->location = $posted_data['location'];
        }
        if (isset($posted_data['country'])) {
            $data->country = $posted_data['country'];
        }
        if (isset($posted_data['profile_completion'])) {
            $data->profile_completion = $posted_data['profile_completion'];
        }
        if (isset($posted_data['address'])) {
            $data->address = $posted_data['address'];
        }
        if (isset($posted_data['personal_identity'])) {
            $data->personal_identity = $posted_data['personal_identity'];
        }
        if (isset($posted_data['description'])) {
            $data->description = $posted_data['description'];
        }
        if (isset($posted_data['email_verification_code'])) {
            if ($posted_data['email_verification_code'] =='NULL') {
                $data->email_verification_code = NULL;
            }
            else{
                $data->email_verification_code = $posted_data['email_verification_code'];
            }
        }
        if (isset($posted_data['profile_image'])) {
            $data->profile_image = $posted_data['profile_image'];
        }
        if (isset($posted_data['phone_number'])) {
            $data->phone_number = $posted_data['phone_number'];
        }
        if (isset($posted_data['user_status'])) {
            $data->user_status = $posted_data['user_status'];
        }
        if (isset($posted_data['register_from'])) {
            $data->register_from = $posted_data['register_from'];
        }
        if (isset($posted_data['last_seen'])) {
            $data->last_seen = $posted_data['last_seen'];
        }
        if (isset($posted_data['email_verified_at'])) {
            $data->email_verified_at = $posted_data['email_verified_at'];
        }
        if (isset($posted_data['time_spent'])) {
            $data->time_spent = $posted_data['time_spent'];
        }
        if (isset($posted_data['theme_mode'])) {
            $data->theme_mode = $posted_data['theme_mode'];
        }
        if (isset($posted_data['remember_token'])) {
            $data->remember_token = $posted_data['remember_token'];
        }
        if (isset($posted_data['verification_token'])) {
            if ($posted_data['verification_token'] =='NULL') {
                $data->verification_token = NULL;
            }
            else{
                $data->verification_token = $posted_data['verification_token'];
            }
        }

        if (isset($posted_data['role'])) {
            $data->role = $posted_data['role'];
        }
        $data->save();

        $data = User::getUser([
            'detail' => true,
            'id' => $data->id
        ]);
        return $data;
    }

    public function deleteUser($id = 0, $where_posted_data = array())
    {
        $is_deleted = false;
        if($id>0){
            $is_deleted = true;
            $data = User::find($id);
        }else{
            $data = User::latest();
        }

        if(isset($where_posted_data) && count($where_posted_data)>0){
            if (isset($where_posted_data['user_status'])) {
                $is_deleted = true;
                $data = $data->where('user_status', $where_posted_data['user_status']);
            }
        }

        if($is_deleted){
            return $data->delete();
        }else{
            return false;
        }
    }
}
