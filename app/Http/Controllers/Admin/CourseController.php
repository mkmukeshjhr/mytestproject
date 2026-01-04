<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseChapter;
use App\Models\CourseOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Storage;



define('LOGINPATH', '/admin/login');

class CourseController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;


    public function courseCategoryList(Request $request)
    {


        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $categories = CourseCategory::query();
                $categoryCount = $categories->count();
                $categories->orderBy('id', 'DESC');
                $categories->skip($paginationStart);
                $categories->take($this->limit);
                $categories = $categories->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.course-category',
                    compact('categories', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #-------------------------------------------------------------------------------------------------------------------------
    public function addCourseCategory(Request $req)
{
    try {
        $validator = Validator::make($req->all(), [
            'name' => 'required|unique:course_categories',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }

        if (!Auth::guard('web')->check()) {
            return redirect(LOGINPATH);
        }

        // Create record first
        $courseCategory = CourseCategory::create([
            'name' => $req->name,
            'image' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $path = null;

        // If image uploaded
        if ($req->hasFile('image')) {
            $file = $req->file('image');
            $imageContent = file_get_contents($file->getRealPath());
            $extension = $file->getClientOriginalExtension();
            $imageName = 'courseCategory_' . $courseCategory->id . '_' . time() . '.' . $extension;

            // Upload using StorageHelper
            $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'course_categories');
        }

        // Update image path in DB
        $courseCategory->image = $path;
        $courseCategory->save();

        return redirect()->route('course-categories-list')->with('message', 'Course Category added successfully!');
    } catch (Exception $e) {
        return dd($e->getMessage());
    }
}


    #----------------------------------------------------------------------------------------------------------------------------------

    public function editCourseCategory(Request $req)
{
    try {
        if (!Auth::guard('web')->check()) {
            return redirect(LOGINPATH);
        }

        $courseCategory = CourseCategory::find($req->filed_id);
        if (!$courseCategory) {
            return back()->with('error', 'Course Category not found!');
        }

        $path = $courseCategory->image;

        // If new image uploaded
        if ($req->hasFile('image')) {
            $file = $req->file('image');
            $imageContent = file_get_contents($file->getRealPath());
            $extension = $file->getClientOriginalExtension();
            $imageName = 'courseCategory_' . $req->filed_id . '_' . time() . '.' . $extension;

            // Delete old image if exists
            if ($courseCategory->image && Storage::exists($courseCategory->image)) {
                Storage::delete($courseCategory->image);
            }

            // Upload new image
            $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'course_categories');
        }

        // Update record
        $courseCategory->name = $req->name;
        $courseCategory->image = $path;
        $courseCategory->updated_at = Carbon::now();
        $courseCategory->save();

        return redirect()->route('course-categories-list')->with('message', 'Course Category updated successfully!');
    } catch (Exception $e) {
        return dd($e->getMessage());
    }
}


    #-----------------------------------------------------------------------------------------------------------------------------------
    public function CourseCategoryStatus(Request $request)
    {
        try {
            $astrologerCategory = CourseCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('course-categories-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #------------------------------------------------------------------------------------------------------------------------------------

    public function CourseList(Request $request)
    {


        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $courses = Course::query();
                $categoryCount = $courses->count();
                $courses->orderBy('id', 'DESC');
                $courses->skip($paginationStart);
                $courses->take($this->limit);
                $courses = $courses->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;

                $categories = CourseCategory::where('isActive', 1)->get();

                return view(
                    'pages.course',
                    compact('courses', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'categories')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #-------------------------------------------------------------------------------------------------------------------------

    public function addCourse(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|string|max:255',
                'course_category_id' => 'required|integer',
                'course_price' => 'nullable|numeric',
                'course_price_usd' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (Auth::guard('web')->check()) {

                // Convert uploaded image to base64
                if ($req->hasFile('image')) {
                    $image = base64_encode(file_get_contents($req->file('image')));
                } else {
                    $image = null;
                }

                // Create Course record first
                $course = Course::create([
                    'name' => $req->name,
                    'course_category_id' => $req->course_category_id,
                    'description' => $req->description,
                    'course_price' => $req->course_price,
                    'course_price_usd' => $req->course_price_usd,
                    'course_badge' => json_encode($req->course_badge),
                    'image' => '',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Handle image upload
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'course_' . $course->id . '_' . $time . '.png';
                        $path = $destinationpath . $imageName;

                        // delete old image if exists
                        if (File::exists($path)) {
                            File::delete($path);
                        }

                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }

                $course->image = $path;
                $course->save();

                return redirect()->route('CourseList-list')->with('message', 'Course added successfully!');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    #---------------------------------------------------------------------------
    public function editCourse(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $course = Course::find($request->filed_id);

                if (!$course) {
                    return back()->with('error', 'Course not found!');
                }

                //  Handle image input (base64 or existing)
                if ($request->hasFile('image')) {
                    $image = base64_encode(file_get_contents($request->file('image')));
                } elseif ($course->image) {
                    $image = $course->image;
                } else {
                    $image = null;
                }

                //  Process image upload
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'course_' . $course->id . '_' . $time . '.png';
                        $path = $destinationpath . $imageName;

                        // delete old image if exists
                        if ($course->image && File::exists($course->image)) {
                            File::delete($course->image);
                        }

                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }

                //  Update fields
                $course->update([
                    'name' => $request->name,
                    'course_category_id' => $request->course_category_id,
                    'description' => $request->description,
                    'course_price' => $request->course_price,
                    'course_price_usd' => $request->course_price_usd,
                    'course_badge' => json_encode($request->course_badge),
                    'image' => $path,
                    'updated_at' => Carbon::now(),
                ]);

                return redirect()->route('CourseList-list')->with('message', 'Course updated successfully!');

            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    #-----------------------------------------------------------------------------------------------------------------------------------
    public function CourseStatus(Request $request)
    {
        try {
            $astrologerCategory = Course::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('CourseList-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #------------------------------------------------------------------------------------------------------------------------------------

    public function deleteCourse(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $course = Course::find($request->del_id);

                if ($course) {
                    $image = $course->image;
                    if (file_exists($image)) {
                        unlink($image);
                    }
                    $course->delete();
                    return redirect()->route('CourseList-list');
                } else {
                    return redirect(LOGINPATH);
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #------------------------------------------------------------------------------------------------------------------------------------

    public function CourseChapterList(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $courseschapter = CourseChapter::query();
                $chaptercount = $courseschapter->count();
                $courseschapter->orderBy('id', 'DESC');
                $courseschapter->skip($paginationStart);
                $courseschapter->take($this->limit);
                $courseschapter = $courseschapter->get();
                $totalPages = ceil($chaptercount / $this->limit);
                $totalRecords = $chaptercount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;

                $courses = Course::where('isActive', 1)->get();

                return view(
                    'pages.course-chapters',
                    compact('courseschapter', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'courses')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #-------------------------------------------------------------------------------------------------------------------------

    public function viewCourseChapter(Request $request)
    {

        try {
            $courses= Course :: where('isActive',1)->get();
            $currency = DB::table('systemflag')
            ->where('name', 'CurrencySymbol')
            ->select('value')
            ->first();

            return view('pages.add-course-chapter',compact('courses','currency'));
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    #-------------------------------------------------------------------------------------------------------------------------

    public function addCourseChapter(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|integer',
                'chapter_name' => 'required|string|max:255',
                'chapter_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'chapter_document' => 'nullable|mimes:pdf,doc,docx|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Auth check
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Handle chapter images
            $imagePaths = [];
            if ($request->hasFile('chapter_images')) {
                foreach ($request->file('chapter_images') as $file) {
                    $name = 'chapter_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/images/chapter_images'), $name);
                    $imagePaths[] = 'public/storage/images/chapter_images/' . $name;
                }
            }

            // Handle chapter document
            $document = null;
            if ($request->hasFile('chapter_document')) {
                $file = $request->file('chapter_document');
                $name = 'chapter_doc_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/chapter_document'), $name);
                $document = 'public/storage/images/chapter_document/' . $name;
            }

            // Save to database
            $chapter = CourseChapter::create([
                'course_id' => $request->course_id,
                'chapter_name' => $request->chapter_name,
                'chapter_description' => $request->chapter_description ?? null,
                'youtube_link' => $request->youtube_link ?? null,
                'chapter_document' => $document,
                'chapter_images' => json_encode($imagePaths), // Store as JSON
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('course-chapter-list')->with('success', 'Chapter added successfully!');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


        #--------------------------------------------------------------------------------------------------------------------------

       public function editCourseChapter($id)
       {
           $chapters = CourseChapter::findOrFail($id);
           $courses= Course :: where('isActive',1)->get();

           return view('pages.add-course-chapter', compact('chapters', 'courses'));
       }

       #----------------------------------------------------------------------------------------------------------------------------

    public function updateCourseChapter(Request $request, $id)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|integer',
                'chapter_name' => 'required|string|max:255',
                'chapter_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'chapter_document' => 'nullable|mimes:pdf,doc,docx|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Auth check
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Find existing chapter
            $chapter = CourseChapter::findOrFail($id);

            // Handle chapter images
            $imagePaths = $chapter->chapter_images ? json_decode($chapter->chapter_images, true) : [];

            // Add existing images from form
            if ($request->has('existing_images')) {
                $existingImages = $request->input('existing_images');
                $imagePaths = array_merge([], $existingImages);
            }

            // Add new uploaded images
            if ($request->hasFile('chapter_images')) {
                foreach ($request->file('chapter_images') as $file) {
                    $name = 'chapter_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('storage/images/chapter_images'), $name);
                    $imagePaths[] = 'public/storage/images/chapter_images/' . $name;
                }
            }

            // Handle chapter document
            $document = $chapter->chapter_document ?? null;
            if ($request->hasFile('chapter_document')) {
                // Delete old document if exists
                if ($document && File::exists(public_path($document))) {
                    File::delete(public_path($document));
                }
                $file = $request->file('chapter_document');
                $name = 'chapter_doc_' . time() . rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/chapter_document'), $name);
                $document = 'public/storage/images/chapter_document/' . $name;
            }

            // Update chapter
            $chapter->update([
                'course_id' => $request->course_id,
                'chapter_name' => $request->chapter_name,
                'chapter_description' => $request->chapter_description ?? null,
                'youtube_link' => $request->youtube_link ?? null,
                'chapter_document' => $document,
                'chapter_images' => json_encode($imagePaths),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('course-chapter-list')->with('success', 'Chapter updated successfully!');

        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



       #------------------------------------------------------------------------------------------------------------------------------

       public function CourseChapterStatus(Request $request)
       {
           try {
               $astrologerCategory = CourseChapter::find($request->status_id);
               if (Auth::guard('web')->check()) {
                   $astrologerCategory->isActive = !$astrologerCategory->isActive;
                   $astrologerCategory->update();
                   return redirect()->route('course-chapter-list');
               } else {
                   return redirect(LOGINPATH);
               }
           } catch (Exception $e) {
               return dd($e->getMessage());
           }
       }

       #------------------------------------------------------------------------------------------------------------------------------------
       public function deleteCourseChapter(Request $request)
       {
           try {
               if (Auth::guard('web')->check()) {
                   $course = CourseChapter::find($request->del_id);

                   if ($course) {
                       $course->delete();
                       return redirect()->route('course-chapter-list');
                   } else {
                       return redirect(LOGINPATH);
                   }
               } else {
                   return redirect(LOGINPATH);
               }
           } catch (Exception $e) {
               return dd($e->getMessage());
           }
       }

       #------------------------------------------------------------------------------------------------------------------------------------
       
        public function courseOrderList(Request $request)
       {
           try {
               if (Auth::guard('web')->check()) {
                   $page = $request->page ?? 1;
                   $paginationStart = ($page - 1) * $this->limit;

                   $query = CourseOrder::with('course','astrologer');

                   $userCount = $query->count();
                   $totalPages = ceil($userCount / $this->limit);
                   $totalRecords = $userCount;
                   $start = ($this->limit * ($page - 1)) + 1;
                   $end = min(($this->limit * $page), $totalRecords);

                       // Clone query for counting records
                  $countQuery = clone $query;
                  // Date filter
                  $from_date = $request->from_date ?? null;
                  $to_date = $request->to_date ?? null;
 
                  if ($from_date && $to_date) {
                      $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                      $countQuery->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                  } elseif ($from_date) {
                      $query->where('created_at', '>=', $from_date . ' 00:00:00');
                      $countQuery->where('created_at', '>=', $from_date . ' 00:00:00');
                  } elseif ($to_date) {
                      $query->where('created_at', '<=', $to_date . ' 23:59:59');
                      $countQuery->where('created_at', '<=', $to_date . ' 23:59:59');
                  }

                   $courseOrderlist = $query->skip($paginationStart)
                                      ->take($this->limit)
                                      ->get();




                    $currency = DB::table('systemflag')
                                      ->where('name', 'CurrencySymbol')
                                      ->select('value')
                                      ->first();

                   return view('pages.course-order-list', compact('courseOrderlist',  'totalPages', 'totalRecords', 'start', 'end', 'page','currency','from_date', 'to_date'));
               } else {
                   return redirect(LOGINPATH);
               }
           } catch (Exception $e) {
               return dd($e->getMessage());
           }
       }


}
