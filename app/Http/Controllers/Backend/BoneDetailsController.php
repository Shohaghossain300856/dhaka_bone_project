<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Backend\BoneDetail;
use App\Models\Backend\BoneDetailImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class BoneDetailsController extends Controller
{
   public function index()
    {
        
        return view('backend.boneCase.boneDetailsCreate');
    }  

      public function create()
      {
          $data = BoneDetail::with('bone','images')->latest()->get();

          return response()->json($data);
      }

public function store(Request $request)
{
    $validated = $request->validate([
        'bonepost_id'             => ['required', 'integer', 'exists:bone_posts,id'],
        'bone_type'           => ['required', 'string', 'max:255'],
        'body_side'           => ['required', 'in:Left,Right,Bilateral'],
        'specimen_condition'  => ['nullable', 'string', 'max:255'],
        'preservation_method' => ['nullable', 'string', 'max:255'],
        'quantity'            => ['required', 'integer', 'min:1'],

        // ✅ multiple files
        'images'              => ['nullable', 'array'],
        'images.*'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
    ]);

    try {
        $detail = DB::transaction(function () use ($request, $validated) {

            // ✅ create bone detail
            $row = BoneDetail::create([
                'bonepost_id'             => $validated['bonepost_id'],
                'bone_type'           => $validated['bone_type'],
                'body_side'           => $validated['body_side'],
                'specimen_condition'  => $validated['specimen_condition'] ?? null,
                'preservation_method' => $validated['preservation_method'] ?? null,
                'quantity'            => $validated['quantity'],
            ]);
            if ($request->hasFile('images')) {

                $dest = public_path('storage/bone_details'); 
                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }

                foreach ($request->file('images') as $file) {
                    if (!$file || !$file->isValid()) continue;

                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dest, $filename);

                    BoneDetailImage::create([
                        'bone_detail_id' => $row->id,
                        'images'         => 'bone_details/' . $filename,
                    ]);
                }
            }
            $row->load([
                'bone:id,title',
                'images:id,bone_detail_id,images',
            ]);

            return $row;
        });
        $detail->images = $detail->images->map(function ($img) {
            return [
                'id'             => $img->id,
                'bone_detail_id' => $img->bone_detail_id,
                'images'         => $img->images,
            ];
        });

        return response()->json([
            'message' => 'Bone detail created successfully',
            'data'    => $detail,
        ], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Failed to create bone detail',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'bonepost_id'             => ['required', 'integer', 'exists:bone_posts,id'],
        'bone_type'           => ['required', 'string', 'max:255'],
        'body_side'           => ['required', 'in:Left,Right,Bilateral'],
        'specimen_condition'  => ['nullable', 'string', 'max:255'],
        'preservation_method' => ['nullable', 'string', 'max:255'],
        'quantity'            => ['required', 'integer', 'min:1'],

        // ✅ multiple new files
        'images'              => ['nullable', 'array'],
        'images.*'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

        // ✅ ids of old images to delete
        'deleted_image_ids'   => ['nullable', 'array'],
        'deleted_image_ids.*' => ['integer'],
    ]);

    try {
        $detail = DB::transaction(function () use ($request, $validated, $id) {

            $row = BoneDetail::with('images')->findOrFail($id);

            // ✅ update main row
            $row->update([
                'bonepost_id'             => $validated['bonepost_id'],
                'bone_type'           => $validated['bone_type'],
                'body_side'           => $validated['body_side'],
                'specimen_condition'  => $validated['specimen_condition'] ?? null,
                'preservation_method' => $validated['preservation_method'] ?? null,
                'quantity'            => $validated['quantity'],
            ]);

            // ✅ delete selected old images (DB + file)
            $deleteIds = $request->input('deleted_image_ids', []);
            if (!empty($deleteIds)) {
                $imgs = BoneDetailImage::where('bone_detail_id', $row->id)
                    ->whereIn('id', $deleteIds)
                    ->get();

                foreach ($imgs as $img) {
                    // file path: public/storage/{images}
                    $fullPath = public_path('storage/' . $img->images);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $img->delete();
                }
            }

            // ✅ add new uploaded images (same logic as store)
            if ($request->hasFile('images')) {
                $dest = public_path('storage/bone_details');
                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }

                foreach ($request->file('images') as $file) {
                    if (!$file || !$file->isValid()) continue;

                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dest, $filename);

                    BoneDetailImage::create([
                        'bone_detail_id' => $row->id,
                        'images'         => 'bone_details/' . $filename,
                    ]);
                }
            }

            // ✅ return fresh data
            $row->load([
                'bone:id,title',
                'images:id,bone_detail_id,images',
            ]);

            return $row;
        });

        // keep same response shape
        $detail->images = $detail->images->map(function ($img) {
            return [
                'id'             => $img->id,
                'bone_detail_id' => $img->bone_detail_id,
                'images'         => $img->images,
            ];
        });

        return response()->json([
            'message' => 'Bone detail updated successfully',
            'data'    => $detail,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Failed to update bone detail',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

  public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $row = BoneDetail::with('images')->findOrFail($id);

                foreach ($row->images as $img) {
                    $filePath = public_path('storage/' . $img->images);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $img->delete();
                }

                $row->delete();
            });

            return response()->json([
                'message' => 'Bone detail deleted successfully'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Delete failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}