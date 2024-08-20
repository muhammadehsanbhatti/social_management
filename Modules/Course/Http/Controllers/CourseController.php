<?php

namespace Modules\Course\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Course\Entities\Course;

class CourseController extends Controller
{
    function __construct()
    {
        // parent::__construct();
        $this->middleware('permission:course-list|course-update|course-delete', ['only' => ['index']]);
        $this->middleware('permission:course-create', ['only' => ['create','store']]);
        $this->middleware('permission:course-update|edit-profile', ['only' => ['edit','update']]);
        $this->middleware('permission:course-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $posted_data = $request->all();
        $posted_data['orderBy_name'] = 'id';
        $posted_data['orderBy_value'] = 'DESC';
        $posted_data['paginate'] = 15;
        $data['courses'] = Course::getCourse($posted_data);

        unset($posted_data['paginate']);
        $data['course'] = Course::all();

        $data['html'] = view('course::course.ajax_records', compact('data'));

        if ($request->ajax()) {
            return $data['html'];
        }
        return view('course::course.list', compact('data'));
    }

    public function ajax_get_course(Request $request)
    {
        $posted_data = $request->all();

        if (isset($posted_data['assign_to'])) {
        } else {
            $posted_data['paginate'] = 10;
            $posted_data['title'] = $request->title;
        }
        $data['courses'] = Course::getCourse($posted_data);

        if ($request->ajax()) {
            return view('course::quiz_assign.partials.courses_dropdown', compact('data'));
        }

        return view('course::course.ajax_records', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('course::course.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $posted_data = $request->all();
        $rules = array(
            'title' => 'required',
            // 'short_description'=>'required'
        );

        $validator = \Validator::make($posted_data, $rules);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), ["error" => $validator->errors()->first()]);
            // return redirect()->back()->withErrors($validator)->withInput();
        } else {
            if ($request->file('course_asset')) {
                $extension = $request->course_asset->getClientOriginalExtension();
                if ($extension == 'pdf') {
                    $file_name = time() . '_' . rand(1000000, 9999999) . '.' . $extension;
                    $filePath =  $request->course_asset->storeAs('course_asset', $file_name, 'public');
                    $filePath = 'storage/course_asset/' . $file_name;
                    $posted_data['course_asset'] = $filePath;
                } else {
                    return $this->sendError('Sorry you can only upload pdf file only');
                }
            }

            if ($request->file('cover_image')) {
                $extension = $request->cover_image->getClientOriginalExtension();
                if (in_array($extension,  ['jpg', 'jpeg', 'png'])) {
                    $file_name = time() . '_' . rand(1000000, 9999999) . '.' . $extension;
                    $filePath =  $request->cover_image->storeAs('course_asset', $file_name, 'public');
                    $filePath = 'course_asset/' . $file_name;
                    $posted_data['cover_image'] = $filePath;
                } else {
                    return $this->sendError('Sorry you can only upload jpg, jpeg, png file only.');
                }
            }

            $CourseObj = new Course();
            $CourseObj->saveUpdateCourse($posted_data);
            return response()->json(["success" => 'Course added successfully']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $posted_data = array();
        $posted_data['id'] = $id;
        $posted_data['detail'] = true;
        $data = Course::getCourse($posted_data);
        return view('course::course.add', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updated_data = $request->all();
        $updated_data['update_id'] = $id;
        $rules = array(
            'title' => 'required',
            // 'short_description'=>'required'
        );

        $validator = \Validator::make($updated_data, $rules);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), ["error" => $validator->errors()->first()]);
            // return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $course_detail = Course::getCourse([
                'id' => $id,
                'detail' => true
            ]);

            if ($request->file('course_asset')) {
                if (isset($course_detail['course_asset'])) {
                    $url = public_path() . '/' . $course_detail['course_asset'];
                    if (file_exists($url)) {
                        unlink($url);
                    }
                }
                $extension = $request->course_asset->getClientOriginalExtension();
                if ($extension == 'pdf') {
                    $file_name = time() . '_' . rand(1000000, 9999999) . '.' . $extension;
                    $filePath =  $request->course_asset->storeAs('course_asset', $file_name, 'public');
                    $filePath = 'storage/course_asset/' . $file_name;
                    $updated_data['course_asset'] = $filePath;
                } else {
                    return $this->sendError('Sorry you can only upload pdf file only');
                }
            }

            if ($request->file('cover_image')) {
                if (isset($course_detail['cover_image'])) {
                    $url = public_path() . '/' . $course_detail['cover_image'];
                    if (file_exists($url)) {
                        unlink($url);
                    }
                }
                $extension = $request->cover_image->getClientOriginalExtension();
                if (in_array($extension,  ['jpg', 'jpeg', 'png'])) {
                    $file_name = time() . '_' . rand(1000000, 9999999) . '.' . $extension;
                    $filePath =  $request->cover_image->storeAs('course_asset', $file_name, 'public');
                    $filePath = 'storage/course_asset/' . $file_name;
                    $updated_data['cover_image'] = $filePath;
                } else {
                    return $this->sendError('Sorry you can only upload jpg, jpeg, png file only.');
                }
            }

            Course::saveUpdateCourse($updated_data);
            return response()->json(["success" => 'Course Updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Course::deleteCourse($id);
        \Session::flash('message', 'Course deleted successfully!');
        return redirect('/course');
    }
}
