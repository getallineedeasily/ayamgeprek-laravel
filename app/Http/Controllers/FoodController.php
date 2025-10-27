<?php

namespace App\Http\Controllers;

use App\Models\Food;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Str;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payload = $request->validate([
            'search' => ['nullable', 'ascii'],
        ]);

        $query = $payload ? '%' . $payload['search'] . '%' : '';

        $search = $payload['search'] ?? '';

        $foods = Food::filteredFood($query)
            ->paginate(perPage: 3)
            ->appends(['search' => $search]);

        return view('admin.food.index', ['foods' => $foods, 'search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'ascii'],
            'price' => ['required', 'numeric', 'min:1', 'integer'],
            'image' => ['required', File::image()->max('2mb')]
        ]);

        $fileName = Str::replace(" ", '-', Str::lower($payload['name'])) . "." . $payload['image']->extension();

        try {
            DB::transaction(function () use ($payload, $fileName) {
                Food::create([
                    'name' => $payload['name'],
                    'price' => $payload['price'],
                    'image' => $fileName
                ]);
            });

            Storage::disk('public')->putFileAs('/images', $payload['image'], $fileName);

            return redirect()->route('admin.view.food')->with('success', 'Berhasil menambahkan menu!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Food $food)
    {
        return view('admin.food.edit', compact('food'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Food $food)
    {
        if ($request->file('image')) {
            $payload = $request->validate([
                'name' => ['required', 'ascii'],
                'price' => ['required', 'numeric', 'min:1', 'integer'],
                'image' => ['required', File::image()->max('2mb')]
            ]);

            $fileName = Str::replace(" ", '-', Str::lower($payload['name'])) . "." . $payload['image']->extension();

        } else {
            $payload = $request->validate([
                'name' => ['required', 'ascii'],
                'price' => ['required', 'numeric', 'min:1', 'integer'],
            ]);

            $fileName = null;
        }

        try {
            if ($request->file('image')) {
                $food->image = $fileName;

                $path = '/images/' . $food->image;
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                Storage::disk('public')->putFileAs('/images', $payload['image'], $fileName);
            }

            $food->name = $payload['name'];
            $food->price = $payload['price'];
            $food->save();
            return redirect()->route('admin.view.food')->with('success', 'Berhasil ubah menu!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        try {
            $path = '/images/' . $food->image;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $food->delete();
            return redirect()->route('admin.view.food')->with('success', 'Berhasil hapus menu!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Ada yang salah! Silahkan coba lagi!');
        }
    }
}
