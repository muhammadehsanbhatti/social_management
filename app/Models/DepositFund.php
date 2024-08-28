<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositFund extends Model
{
    use HasFactory;

    public static function getDepositFund($posted_data = array())
    {
        $query = DepositFund::latest();

        if (isset($posted_data['id'])) {
            $query = $query->where('terms_conditions.id', $posted_data['id']);
        }
        if (isset($posted_data['user_id'])) {
            $query = $query->where('terms_conditions.user_id', $posted_data['user_id']);
        }
        if (isset($posted_data['credit_amount'])) {
            $query = $query->where('terms_conditions.credit_amount', $posted_data['credit_amount']);
        }
        if (isset($posted_data['payment_type'])) {
            $query = $query->where('terms_conditions.payment_type', $posted_data['payment_type']);
        }
        if (isset($posted_data['payment_status'])) {
            $query = $query->where('terms_conditions.payment_status', $posted_data['payment_status']);
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

    public function saveUpdateDepositFund($posted_data = array(), $where_posted_data = array())
    {
        if (isset($posted_data['update_id'])) {
            $data = DepositFund::find($posted_data['update_id']);
        } else {
            $data = new DepositFund;
        }

        if (isset($posted_data['user_id'])) {
            $data->user_id = $posted_data['user_id'];
        }

        if (isset($posted_data['credit_amount'])) {
            $data->credit_amount = $posted_data['credit_amount'];
        }

        if (isset($posted_data['payment_type'])) {
            $data->payment_type = $posted_data['payment_type'];
        }

        if (isset($posted_data['payment_status'])) {
            $data->payment_status = $posted_data['payment_status'];
        }

        $data->save();

        $data = DepositFund::getDepositFund([
            'detail' => true,
            'id' => $data->id
        ]);
        return $data;
    }

    public function deleteDepositFund($id = 0, $where_posted_data = array())
    {
        $is_deleted = false;
        if($id>0){
            $is_deleted = true;
            $data = DepositFund::find($id);
        }else{
            $data = DepositFund::latest();
        }

        if(isset($where_posted_data) && count($where_posted_data)>0){
            if (isset($where_posted_data['user_id'])) {
                $is_deleted = true;
                $data = $data->where('user_id', $where_posted_data['user_id']);
            }
        }

        if($is_deleted){
            return $data->delete();
        }else{
            return false;
        }
    }
}
