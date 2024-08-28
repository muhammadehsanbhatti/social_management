<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    use HasFactory;
    public static function getTermsCondition($posted_data = array())
    {
        $query = TermsCondition::latest();

        if (isset($posted_data['id'])) {
            $query = $query->where('terms_conditions.id', $posted_data['id']);
        }
        if (isset($posted_data['title'])) {
            $query = $query->where('terms_conditions.title', $posted_data['title']);
        }
        if (isset($posted_data['description'])) {
            $query = $query->where('terms_conditions.description', $posted_data['description']);
        }

        $query->select('terms_conditions.*');

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

    public function saveUpdateTermsCondition($posted_data = array(), $where_posted_data = array())
    {
        if (isset($posted_data['update_id'])) {
            $data = TermsCondition::find($posted_data['update_id']);
        } else {
            $data = new TermsCondition;
        }

        if (isset($posted_data['title'])) {
            $data->title = $posted_data['title'];
        }

        if (isset($posted_data['description'])) {
            $data->description = $posted_data['description'];
        }

        $data->save();

        $data = TermsCondition::getTermsCondition([
            'detail' => true,
            'id' => $data->id
        ]);
        return $data;
    }

    public function deleteTermsCondition($id = 0, $where_posted_data = array())
    {
        $is_deleted = false;
        if($id>0){
            $is_deleted = true;
            $data = TermsCondition::find($id);
        }else{
            $data = TermsCondition::latest();
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
