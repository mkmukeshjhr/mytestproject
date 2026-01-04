<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Deity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeityController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function index(Request $request)
    {
        try {
            $page = $request->page ? $request->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $deities = Deity::orderBy('id', 'DESC')->skip($paginationStart)->take($this->limit)->get();
            $bannerCount = $deities->count();

            $totalPages = ceil($bannerCount / $this->limit);
            $totalRecords = $bannerCount;
            $start = $paginationStart + 1;
            $end = min($paginationStart + $this->limit, $totalRecords);

            return view('pages.deities.index', compact('deities', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:deities,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'short_desc' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['name', 'short_desc', 'is_active']);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('deities', 'public');
            }
            Deity::create($data);
            return back()->with('success', 'Deity created successfully.');
        } catch (\Exception $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, Deity $deity)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:deities,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'short_desc' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['name', 'short_desc', 'is_active']);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($deity->image && Storage::disk('public')->exists($deity->image)) {
                    Storage::disk('public')->delete($deity->image);
                }
                $data['image'] = $request->file('image')->store('deities', 'public');
            }

            $deity->update($data);
            return back()->with('success', 'Deity updated successfully.');
        } catch (\Exception $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deity $deity)
    {
        try {
            if ($deity->image && Storage::disk('public')->exists($deity->image)) {
                Storage::disk('public')->delete($deity->image);
            }
            $deity->delete();
            return back()->with('success', 'Deity deleted successfully.');
        } catch (\Exception $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function statusUpdate($id)
    {
        try {
            $deity = Deity::find($id);
            $deity->is_active = $deity->is_active ? 0 : 1;
            $deity->save();
            return back()->with('success', 'Deity status update successfully.');
        } catch (\Exception $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
