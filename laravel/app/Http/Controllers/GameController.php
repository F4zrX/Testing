<?php
// Menentukan namespace yang digunakan
namespace App\Http\Controllers;

// Mengimpor kelas Request dan Game
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Game;
use App\Models\Cart;
use App\Models\Transaksi;
use App\Models\Slider;
use App\Models\Kategori;
use Mail;
use App\Mail\MailSend;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// Mendefinisikan kelas GameController yang mewarisi Controller
class GameController extends Controller
{
    
    // Menampilkan daftar game
    public function index()
    {
        $games = Game::paginate(5); // Menampilkan 5 data per halaman
        return view('game.index', ['games' => $games]);
    }
    // Menampilkan formulir untuk menambahkan game baru
    public function create()
    {
        $categories = Kategori::all(); // Ambil semua data kategori
        return view('form-data', compact('categories')); // Kirim data kategori ke view
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:3|max:50',
            'kategori_id' => 'required|exists:kategori,id', // Pastikan kategori_id yang dimasukkan ada di tabel kategori
            'harga' => 'required|min:1|max:20',
            'platform' => 'required|min:2|max:50',
            'rating' => 'required|size:4',
            'deskripsi' => 'required|min:5',
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $imageName = time() . '.' . $request->image->extension();

        $game = new Game(); // Buat instance baru dari model Game
        $game->nama = $request->nama;
        $game->kategori_id = $request->kategori_id; // Simpan kategori_id dari form
        $game->harga = $request->harga;
        $game->platform = $request->platform;
        $game->rating = $request->rating;
        $game->deskripsi = $request->deskripsi;
        $game->image = $imageName;

        $game->save();

        Storage::putFileAs('public/Image_data', $request->image, $imageName);

        return redirect()->route('game.index')->with('dataAdded', 'Data berhasil ditambahkan');
    }
    
    // Menampilkan detail game
    public function show(Game $game)
    {
        return view('game.show', ['game' => $game]);
    }
    // Menampilkan formulir untuk mengedit game
    public function edit(Game $game)
    {
        $categories = Kategori::all();
        return view('game.edit', ['game' => $game, 'categories' => $categories]);
    }

    // Memperbarui data game yang ada
    public function update(Request $request, Game $game)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|min:3|max:50',
            'kategori_id' => 'required|exists:kategori,id', // Pastikan kategori_id yang dimasukkan ada di tabel kategori
            'harga' => 'required|min:1|max:20',
            'platform' => 'required|min:2|max:50',
            'rating' => 'required|size:4',
            'deskripsi' => 'required|min:5',
            'image' => 'image',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        if ($request->hasFile('image')) {
            $old_image = $game->image;
            Storage::delete('public/Image_data/' . $old_image);
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/Image_data', $imageName);

            $game->image = $imageName;
        }

        $game->nama = $request->nama;
        $game->kategori_id = $request->kategori_id; // Update kategori_id yang diubah
        $game->harga = $request->harga;
        $game->platform = $request->platform;
        $game->rating = $request->rating;
        $game->deskripsi = $request->deskripsi;

        $game->save();

        return redirect()->route('games.show', ['game' => $game->id])->with('dataEdit', 'Data berhasil edit');
    }
    
    // Menghapus data game
    public function destroy(Game $game)
    {   
        $game->delete();
        return redirect()->route('game.index')->with('dataDeleted', 'Data berhasil dihapus');
    }

    public function indexSlider()
    {
        $sliders = Slider::all();
        return view('game.indexslide', ['sliders' => $sliders]);
    }

    // Menampilkan formulir untuk menambahkan slider baru
    public function createSlider()
    {
        return view('game.createslide');
    }

    // Menyimpan slider baru ke dalam database
    public function storeSlider(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:50',
            'image' => 'required|image',
        ]);

        // Jika validasi gagal, kembalikan dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Simpan gambar ke penyimpanan
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/images', $imageName);

        // Simpan data slider ke dalam database
        Slider::create([
            'title' => $request->title,
            'image' => $imageName,
        ]);

        return redirect()->route('game.indexslide')->with('dataAdd', 'Slider berhasil ditambahkan');
    }

    public function gamelogout()
    {
        Auth::logout(); // Melakukan logout pengguna

        return redirect()->route('login'); 
    }

    public function deleteSlider($id)
    {
        // Temukan slider berdasarkan ID
        $slider = Slider::find($id);

        // Jika slider tidak ditemukan, kembalikan dengan pesan error
        if (!$slider) {
            return redirect()->route('game.indexslide')->with('error', 'Slider not found.');
        }

        // Hapus gambar dari penyimpanan
        Storage::delete('public/images/' . $slider->image);

        // Hapus slider dari database
        $slider->delete();

        return redirect()->route('game.indexslide')->with('dataDeleted', 'Slider berhasil dihapus');;
    }

    public function kindex()
    {
        $categories = Kategori::all();
        return view('game.kindex', compact('categories'));
    }

    public function kcreate()
    {
        return view('game.kcreate');
    }

    public function kstore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:kategori',
        ]);

        Kategori::create([
            'name' => $request->name,
        ]);

        return redirect()->route('game.kindex')->with('dataAdd', 'Kategori berhasil ditambahkan.');
    }

    public function kdelete($id)
    {
        // Temukan kategori berdasarkan ID
        $kategori = Kategori::find($id);

        // Jika kategori tidak ditemukan, kembalikan dengan pesan error
        if (!$kategori) {
            return redirect()->route('game.kindex')->with('error', 'Kategori not found.');
        }

        // Hapus kategori dari database
        $kategori->delete();

        return redirect()->route('game.kindex')->with('dataDeleted', 'Kategori deleted successfully.');
    }


}
