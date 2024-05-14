<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Game;
use App\Models\Wishlist;
use App\Models\Rating;
use App\Models\Slider;
use App\Enums\LibraryStatus;
use App\Models\Library;
use App\Models\Cart;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    
    public function index(Request $request)
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();
        $games = Game::with('kategori')->get();
        
        // Mengambil data game
        if($request->has('search')){
            $searchTerm = $request->search;
            $games = Game::where('nama', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('kategori', function ($query) use ($searchTerm) {
                            $query->where('id', $searchTerm);
                        })
                        ->get();
        } else {
            $games = Game::all();
        }

        // Mengambil data dari tabel Library berdasarkan user ID
        $libraries = Library::where('user_id', $userId)->get();

        // Mengambil data slider
        $sliders = Slider::all(); // Gantilah sesuai dengan model dan kondisi yang sesuai dengan aplikasi Anda

        return view('landing.index', compact('games', 'libraries', 'sliders'));
    }

    

    public function browse(Request $request)
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();
        
        // Menginisialisasi query untuk mengambil data permainan
        $query = Game::query()->with('kategori');
        
        // Pencarian berdasarkan nama atau kategori jika parameter search diberikan
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('nama', 'like', '%' . $searchTerm . '%')
                ->orWhereHas('kategori', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
        }

        // Mendapatkan data permainan sesuai dengan query yang telah dibuat
        $games = $query->get();

        // Mengambil semua kategori
        $categories = Kategori::all();

        // Mengembalikan tampilan dengan data permainan yang sudah difilter dan diurutkan
        return view('landing.browse', compact('games', 'categories'));
    }
    
    public function browseByCategory($categoryId)
    {
        // Mendapatkan data permainan berdasarkan kategori yang dipilih
        $games = Game::where('kategori_id', $categoryId)->get();
    
        // Mendapatkan kategori yang terkait dengan permainan yang ditampilkan
        $categories = Kategori::whereIn('id', $games->pluck('kategori_id')->unique())->get();
    
        // Mengembalikan tampilan dengan data permainan yang sudah difilter dan diurutkan
        return view('landing.browse', compact('games', 'categories'));
    }
    

    public function sortByName(Request $request)
    {
        // Menginisialisasi query untuk mengambil data permainan dan mengurutkannya berdasarkan nama secara ascending
        $games = Game::orderBy('nama', 'asc')->get();

        // Mendapatkan semua kategori
        $categories = Kategori::all();

        // Mengembalikan tampilan dengan data permainan yang sudah diurutkan
        return view('landing.browse', compact('games', 'categories'));
    }

    public function sortByPrice()
    {
        $games = Game::orderBy('harga', 'desc')->get();
        
        // Mendapatkan semua kategori
        $categories = Kategori::all();

        // Mengembalikan tampilan dengan data permainan yang sudah diurutkan
        return view('landing.browse', compact('games', 'categories'));
    }  

    public function sortByPriceLowToHigh()
    {
        // Mendapatkan data permainan dan menyortirnya berdasarkan harga terendah ke tertinggi
        $games = Game::orderBy('harga', 'asc')->get();

        // Mengambil semua kategori
        $categories = Kategori::all();

        // Mengembalikan tampilan dengan data permainan yang sudah disortir dan kategori
        return view('landing.browse', compact('games', 'categories'));
    }

    public function cart()
    {
        $userId = Auth::id(); // Mendapatkan ID pengguna yang sedang login
        $carts = Cart::where('user_id', $userId)->get();

        // Tampilkan keranjang jika ada item
        return view('landing.cart', compact('carts'));
    }

    public function delete(Cart $cart)
    {
        // Hapus item dari keranjang
        $cart->delete();

        // Check if there are still items in the cart
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->get();
        dd($cart);

        if ($carts->isEmpty()) {
            // Jika keranjang kosong, kirimkan pesan dan tetap di halaman keranjang
            return redirect()->route('landing.cart')->with('pesan', "Hapus data $cart->nama berhasil. Keranjang Anda kosong.");
        } else {
            // Jika masih ada item, kirimkan pesan dan tetap di halaman keranjang
            return redirect()->route('landing.cart')->with('pesan', "Hapus data $cart->nama berhasil.");
        }
    }

    public function wishlist(Request $request)
    {
        // Mendapatkan data wishlist berdasarkan user ID
        $query = Wishlist::where('user_id', Auth::id());

        // Pencarian berdasarkan nama jika parameter search diberikan
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('game', function ($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%');
            });
        }

        $wishlists = $query->get();

        // Mengembalikan tampilan wishlist dengan data yang sudah difilter
        return view('landing.wishlist', compact('wishlists'));
    }


    public function profile()
    {
        // Mengambil ID pengguna yang sedang login
        $userId = auth()->id();

        // Mengambil data dari tabel Library berdasarkan user ID
        $libraries = Library::where('user_id', $userId)->get();

        // Menghitung total pengeluaran dari semua barang yang ada di library
        $totalPengeluaran = $libraries->sum(function($item) {
            return $item->game->harga; // Mengambil harga dari relasi game
        });
        
        // Kirim data transaksi dan string acak ke tampilan blade
        return view('landing.profile', compact('libraries', 'totalPengeluaran'));
    }

    public function upload(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('image')) {
            // Proses upload gambar ke direktori 'public/user'
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            Storage::putFileAs('public/user', $image, $imageName);

            // Simpan nama file gambar ke dalam kolom 'image' pada tabel users
            $user->image = $imageName;
        }

        // Simpan perubahan
        $user->save();

        // Tambahkan pesan flash
        session()->flash('success', 'Foto profil berhasil diubah');

        return redirect()->route('landing.profile');
    }

    
    public function updateStatus(Request $request, Library $library)
    {
        $status = $request->input('status');
        
        // Pastikan status yang diberikan valid
        if (in_array($status, [LibraryStatus::BELUM_DIBAYAR, LibraryStatus::DIBAYAR, LibraryStatus::TERKIRIM, LibraryStatus::SELESAI])) {
            $library->status = $status;
            $library->save();

            return redirect()->back()->with('success', 'Status berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }
    }

    public function detail($id){
        $game = Game::find($id);
        return view('landing.detail', compact('game'));
    }

    public function storecart(Request $request)
    {
        // Check if user is authenticated
        if(auth()->check()) {
            $gameId = $request->game_id;
            $userId = Auth::user()->id;
            
            // Check if the game already exists in the user's cart
            $existingCart = Cart::where('user_id', $userId)
                                ->where('game_id', $gameId)
                                ->first();

            if($existingCart) {
                // If the game already exists, increment the quantity
                $existingCart->increment('qty');
            } else {
                // If the game doesn't exist, create a new cart item
                $cart = new Cart;
                $cart->game_id = $gameId; // Simpan ID game
                $cart->user_id = $userId;
                $cart->qty = 1;
                $cart->save();
            }

            // Redirect to the cart page with the new cart ID
            return redirect()->route('landing.cart')->with('success', 'Item added to cart successfully.');
        } else {
            // Handle the case where the user is not authenticated
            // You can redirect them to the login page or handle it in your own way
            return redirect('login')->with('error', 'You need to be logged in to perform this action.');
        }
    }
    
    public function storetrans(Request $request)
    {
        // Ambil data keranjang untuk pengguna yang sedang login
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        // Pastikan keranjang tidak kosong
        if ($carts->isEmpty()) {
            // Jika keranjang kosong, arahkan pengguna kembali ke halaman keranjang atau berikan pesan kesalahan
            return redirect()->route('landing.cart')->with('error', 'Your cart is empty.');
        }

        // Inisialisasi total harga dan detail transaksi
        $totalPrice = 0;
        $items = [];

        // Loop melalui keranjang untuk menghitung total harga dan menyiapkan detail transaksi
        foreach ($carts as $cart) {
            // Hitung total harga setiap barang dengan mengalikan harga dengan jumlah
            $itemPrice = $cart->game->harga * $cart->qty;
            $totalPrice += $itemPrice;

            // Tambahkan item ke detail transaksi
            $items[] = [
                'id' => $cart->game_id,
                'price' => $cart->game->harga,
                'quantity' => $cart->qty,
                'name' => $cart->game->nama,
                'category' => $cart->game->kategori_id,
                // tambahkan detail lainnya jika diperlukan
            ];
        }

        // Generate unique order ID
        $orderId = uniqid();

        // Atur detail transaksi
        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $totalPrice, // Total harga dari semua item di keranjang
            ),
            'item_details' => $items, // Detail setiap item di keranjang
        );

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        // Dapatkan token pembayaran dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Simpan data transaksi untuk setiap item di keranjang
        foreach ($carts as $cart) {
            // Simpan transaksi untuk setiap item di keranjang
            $transaksi = new Transaksi();
            $transaksi->user_id = $cart->user_id;
            $transaksi->game_id = $cart->game_id;
            $transaksi->order_id = $orderId;
            $transaksi->snap_token = $snapToken;

            // Menyimpan transaksi
            $transaksi->save();
        }

        // Setelah data transaksi berhasil disimpan, Anda dapat mengalihkan pengguna ke halaman pembayaran atau halaman konfirmasi
        return redirect()->route('checkout')->with('success', 'Your order has been placed successfully.');
    }

    public function history()
    {
        return view("landing.history");
    }    
    
    public function cartup(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'quantity' => 'required|integer|min:1', // Pastikan jumlahnya merupakan angka dan minimal 1
        ]);

        // Temukan item keranjang dengan ID yang diberikan
        $cart = Cart::findOrFail($id);

        // Perbarui jumlah barang dalam keranjang
        $cart->update([
            'qty' => $request->quantity,
        ]);

        // Kirim respons JSON yang menandakan pembaruan berhasil
        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
        ]);
    }

    // public function update(Request $request, $id){
    //     // Memperbarui kuantitas pada keranjang
    //     $cart = Cart::find($id);
    //     $cart->qty = $request->quantity;
    //     $cart->save();

    //     // Memberikan respons JSON yang menandakan pembaruan berhasil
    //     return response()->json([
    //         'success' => true
    //     ]);
    // }

    public function checkout()
    {
        // Mendapatkan data keranjang untuk pengguna yang sedang login
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        // Memastikan keranjang tidak kosong
        if ($carts->isEmpty()) {
            // Jika keranjang kosong, Anda dapat mengalihkan pengguna ke halaman lain atau memberikan pesan kesalahan
            return redirect()->route('landing.cart')->with('error', 'Your cart is empty.');
        }

        // Hitung total harga dari semua item di keranjang
        $totalPrice = 0;
        foreach ($carts as $cart) {
            $totalPrice += $cart->game->harga * $cart->qty;
        }

        // Mendapatkan snap token dari tabel transaksi
        $transaksi = Transaksi::where('user_id', Auth::user()->id)->latest()->first(); // Ambil transaksi terbaru
        $snapToken = $transaksi ? $transaksi->snap_token : null; // Ambil snap token jika transaksi tersedia

        // Mengirimkan data keranjang, total harga, dan snap token ke tampilan checkout
        return view('landing.checkout', compact('carts', 'totalPrice', 'snapToken'));
    }

    // public function paymentSuccess(Request $request)
    // {
    //     // Verifikasi pembayaran berhasil di sini, Anda mungkin perlu menggunakan SDK pembayaran yang Anda gunakan
    //     // Jika pembayaran berhasil
    //     if ($request->payment_status == 'success') {
    //         // Mendapatkan transaksi terbaru pengguna
    //         $transaksi = Transaksi::where('user_id', Auth::user()->id)->latest()->first();

    //         // Memastikan transaksi tersedia
    //         if ($transaksi) {
    //             // Mendapatkan data game dari transaksi
    //             $game_id = $transaksi->game_id;
    //         }

    //         // Jika pembayaran tidak berhasil, mungkin Anda ingin memberikan pesan kesalahan atau mengalihkan pengguna ke halaman lain
    //         return redirect()->route('checkout.process')->with('error', 'Payment failed.');
    //     }

    //     // Redirect ke halaman detailtrans jika pembayaran berhasil
    //     return redirect()->route('landing.detailtrans')->with('success', 'Payment successful.');
    // }   

    public function detailtrans()
    {
         // Mengambil data transaksi berdasarkan user yang sedang login
         $transaksis = Transaksi::where('user_id', Auth::id())->get();
         $carts = Cart::all(); // Ubah sesuai dengan logika aplikasi Anda
         

        // Jika tidak ada data carts, Anda bisa menangani kasus ini sesuai kebutuhan aplikasi Anda

         // Kirim data transaksi ke tampilan blade
        return view('landing.detailtrans', compact('transaksis', 'carts'));
    }


    public function logout()
    {
        Auth::logout(); // Melakukan logout pengguna

        return redirect()->route('landing.index'); // Mengalihkan pengguna ke halaman utama setelah logout
    }

    public function addToWishlist(Game $game)
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();

        // Periksa apakah game sudah ada dalam wishlist pengguna
        $wishlistItem = Wishlist::where('user_id', $userId)
                                ->where('game_id', $game->id)
                                ->first();

        // Jika game sudah ada dalam wishlist, beri notifikasi
        if ($wishlistItem) {
            return redirect()->back()->with('warning', 'The game is already in your wishlist.');
        }

        // Jika game belum ada dalam wishlist, tambahkan ke dalam wishlist
        Wishlist::create([
            'user_id' => $userId,
            'game_id' => $game->id,
        ]);

        return redirect()->back()->with('success', 'The game has been added to your wishlist.');
    }

    public function removeFromWishlist(Request $request, $id)
    {
        try {
            // Cari wishlist berdasarkan ID
            $wishlist = Wishlist::findOrFail($id);
            
            // Hapus wishlist
            $wishlist->delete();

            return response()->json(['message' => 'Wishlist removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to remove wishlist.', 'error' => $e->getMessage()], 500);
        }
    }

    public function library(Request $request)
    {
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();
        
        // Menginisialisasi query untuk mengambil data permainan
        $games = Game::with('kategori')->get();

        // Mengambil data dari tabel Library berdasarkan user ID
        $libraries = Library::where('user_id', $userId)->get();

        return view('landing.library', compact('libraries', 'games'));  
    }

    public function librarydetails(Request $request){
        // Mendapatkan ID pengguna yang sedang login
        $userId = Auth::id();
        
        // Mendapatkan ID game dari permintaan
        $gameId = $request->input('id');
        
        // Mengambil data dari tabel Library berdasarkan user ID dan ID game
        $library = Library::where('user_id', $userId)->where('game_id', $gameId)->first();
        
        // Pastikan $library tidak null
        if ($library) {
            // Mengambil data permainan yang terkait dengan library
            $game = $library->game;
    
            // Mengambil data kategori dari permainan yang terkait
            $category = $game->kategori ?? null;
        } else {
            // Jika $library null, atur $game dan $category ke null
            $game = null;
            $category = null;
        }
    
        // Mengambil semua kategori
        $categories = Kategori::all();
        
        return view('landing.librarydetails', compact('library', 'game', 'category', 'categories'));
    }       

    public function generateRandomNumber()
    {
        // Generate random number between 0 and 100
        $randomNumber = random_int(0, 100);

        // Generate random number between 0 and 1
        $randomFloat = rand(0, 1);

        return view('landing.profile', compact('randomNumber', 'randomFloat'));
    }
    

}
