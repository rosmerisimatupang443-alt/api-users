
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// GET: Menampilkan semua user
Route::get('/allusers', function () {
    return response()->json([
        'success' => true,
        'message' => 'Data semua user berhasil diambil',
        'data' => User::all()
    ], 200);
});

// POST: Membuat user baru
Route::post('/createusers', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8'
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password'])
    ]);

    return response()->json([
        'success' => true,
        'message' => 'User berhasil dibuat',
        'data' => $user
    ], 201);
});

// PUT: Mengubah data user berdasarkan ID
Route::put('/updateusers/{id}', function (Request $request, string $id) {
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
        'password' => 'sometimes|nullable|string|min:8',
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User berhasil diperbarui',
        'data' => $user
    ], 200);
});

// DELETE: Menghapus user berdasarkan ID
Route::delete('/deleteusers/{id}', function (string $id) {
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'User berhasil dihapus'
    ], 200);
});
