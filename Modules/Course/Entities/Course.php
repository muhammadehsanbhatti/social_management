<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Course\Database\factories\CourseFactory::new();
    }
    

    public static function getCourse($posted_data = array())
    {
        $query = Course::latest();

        if (isset($posted_data['id'])) {
            $query = $query->where('courses.id', $posted_data['id']);
        }
        if (isset($posted_data['title'])) {
            $query = $query->where('courses.title', 'like', '%' . $posted_data['title'] . '%');
        }
        if (isset($posted_data['short_description'])) {
            $query = $query->where('courses.short_description', $posted_data['short_description']);
        }
        if (isset($posted_data['course_asset'])) {
            $query = $query->where('courses.course_asset', $posted_data['course_asset']);
        }
        $query->select('courses.*');

        if(isset($posted_data['assign_to'])){
            $query = $query->with(['QuizzesAssignData'  => function ($query) use ($posted_data) {
                $query = $query->where('quiz_assigns.assign_to', 'Course');
            }]);
        }
        
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

    public function saveUpdateCourse($posted_data = array(), $where_posted_data = array())
    {
        if (isset($posted_data['update_id'])) {
            $data = Course::find($posted_data['update_id']);
        } else {
            $data = new Course;
        }

        if (isset($posted_data['title'])) {
            $data->title = $posted_data['title'];
        }
        if (isset($posted_data['short_description'])) {
            $data->short_description = $posted_data['short_description'];
        }
        if (isset($posted_data['duration'])) {
            $data->duration = $posted_data['duration'];
        }
        if (isset($posted_data['cover_image'])) {
            $data->cover_image = $posted_data['cover_image'];
        }
        if (isset($posted_data['course_asset'])) {
            $data->course_asset = $posted_data['course_asset'];
        }

        $data->save();
        
        $data = Course::getCourse([
            'detail' => true,
            'id' => $data->id
        ]);
        return $data;
    }
    
    public function deleteCourse($id = 0, $where_posted_data = array())
    {
        $is_deleted = false;
        if($id>0){
            $is_deleted = true;
            $data = Course::find($id);
        }else{
            $data = Course::latest();
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
