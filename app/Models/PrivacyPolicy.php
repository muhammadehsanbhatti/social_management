<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;
    public static function getPrivacyPolicy($posted_data = array())
    {
        $query = PrivacyPolicy::latest();

        if (isset($posted_data['id'])) {
            $query = $query->where('privacy_policies.id', $posted_data['id']);
        }
        if (isset($posted_data['title'])) {
            $query = $query->where('privacy_policies.title', $posted_data['title']);
        }
        if (isset($posted_data['description'])) {
            $query = $query->where('privacy_policies.description', $posted_data['description']);
        }

        $query->select('privacy_policies.*');

        $query->getQuery()->orders = null;
        if (isset($posted_data['orderBy_name']) && isset($posted_data['orderBy_value'])) {
            $query->orderBy($posted_data['orderBy_name'], $posted_data['orderBy_value']);
        } else {
            $query->orderBy('id', 'DESC');
        }

        if (isset($posted_data['paginate'])) {
            $result = $query->paginate($posted_data['paginate']);
        } else {
            if (isset($posted_data['detail'])) {
                $result = $query->first();
            } else if (isset($posted_data['count'])) {
                $result = $query->count();
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

    public function saveUpdatePrivacyPolicy($posted_data = array(), $where_posted_data = array())
    {
        if (isset($posted_data['update_id'])) {
            $data = PrivacyPolicy::find($posted_data['update_id']);
        } else {
            $data = new PrivacyPolicy;
        }

        if (isset($posted_data['title'])) {
            $data->title = $posted_data['title'];
        }

        if (isset($posted_data['description'])) {
            $data->description = $posted_data['description'];
        }

        $data->save();

        $data = PrivacyPolicy::getPrivacyPolicy([
            'detail' => true,
            'id' => $data->id
        ]);
        return $data;
    }

    public function deletePrivacyPolicy($id = 0, $where_posted_data = array())
    {
        $is_deleted = false;
        if($id>0){
            $is_deleted = true;
            $data = PrivacyPolicy::find($id);
        }else{
            $data = PrivacyPolicy::latest();
        }

        if(isset($where_posted_data) && count($where_posted_data)>0){
            if (isset($where_posted_data['title'])) {
                $is_deleted = true;
                $data = $data->where('title', $where_posted_data['title']);
            }
        }

        if($is_deleted){
            return $data->delete();
        }else{
            return false;
        }
    }
}
