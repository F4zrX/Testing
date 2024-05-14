<!-- /*
* Bootstrap 5
* Template Name: Furni
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="favicon.png">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="assets/css/tiny-slider.css" rel="stylesheet">
		<link href="assets/css/style.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/fontawesome.css">
		<link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
		<link rel="stylesheet" href="assets/css/owl.css">
		<link rel="stylesheet" href="assets/css/animate.css">
		<title>Furni Free Bootstrap 5 Template for Furniture and Interior Design Websites by Untree.co </title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<header class="header-area header-sticky">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<nav class="main-nav">
							<!-- ***** Logo Start ***** -->
							<a href="{{ route('landing.index') }}" class="logo">
								<img src="assets/images/logo.png" alt="">
							</a>
							<!-- ***** Logo End ***** -->
							<!-- ***** Search End ***** -->
							<div class="search-input">
							  <form id="search" action="#">
								<input type="text" placeholder="Type Something" id='searchText' name="searchKeyword" onkeypress="handle" />
								<i class="fa fa-search"></i>
							  </form>
							</div>
							<!-- ***** Search End ***** -->
							<!-- ***** Menu Start ***** -->
							<ul class="nav">
                        <li><a href="{{ route('landing.index') }}" class="active">Home</a></li>
                        <li><a href="{{ route('landing.browse') }}">Browse</a></li>
                        @auth
                            <li><a href="{{ route('landing.cart', ['id' => Auth::id()]) }}">Cart</a></li>
                        @else
                            <li><a href="{{ route('landing.history') }}">Cart</a></li>
                        @endauth
                        <li><a href="{{ route('landing.wishlist') }}">Wishlist</a></li>
                        <li>
                            <a href="{{ route('landing.profile') }}" >
                                Profile 
                                @auth
                                    @if(Auth::user()->image)
                                        <img src="{{ asset('storage/user/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="img-fluid rounded-circle" >
                                    @else
                                        <img src="{{ asset('assets/images/profile.jpg') }}" alt="Default Profile Image">
                                    @endif
                                @else
                                    <img src="{{ asset('assets/images/profile.jpg') }}" alt="Default Profile Image">
                                @endauth
                            </a>
                        </li>
                    </ul>   
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
						</nav>
					</div>
				</div>
			</div>
		  </header>
		<!-- End Header/Navigation -->
		<!-- Start Isi -->
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="page-content">
						<div class="heading-section">
							<h4>Payment</h4>
						</div>
						@foreach($carts as $cart)
						<div class="content-section">
							<div class="row">
								<div class="col-md-2">
									<div id="fotoB">
										<img src="{{ asset('storage/image_data/' . $cart->game->image) }}" alt="{{ $cart->game->image }}" class="img-fluid">
									</div>
								</div>
								<div class="col-md-2">
									<div class="" id="barang">
										<td class="">
											<h2 class="h6 text-white mt-0">{{$cart->game->nama}}</h2>
											<input type="hidden" class="game_id" value="{{ $cart->game_id }}">
										</td>
									</div>
									<td class="h6 text-white">IDR. {{ number_format(floatval($cart->game->harga), 0, ',', '.') }}</td>
								</div>
							</div>
							<br>
							@endforeach
							<hr>
								<div class="heading-section">
									<h4 class="text-white">Detail Pembayaran</h4>
								</div>
								<div class="content-section">
									<div class="row align-items-center">
										<div class="col-sm-4">
											<div>
												<h4 class="h5 text-white mt-0">Total Pesanan</h4>
												<h4 class="h5 text-white mt-0">Biaya Admin</h4>
												<hr>
												<h4 class="h5 text-white mt-0">Total Pembayaran</h4>
											</div>
										</div>
										<div class="col-sm-4">
											<div>
												@php
													$totalPrice = 0; // Inisialisasi total harga
												@endphp
												@foreach ($carts as $cart)
													@php
														$totalPrice += $cart->game->harga * $cart->qty; // Menambahkan harga total dari setiap item di keranjang
													@endphp
												@endforeach
												<h4 class="h5 text-white">IDR. {{ number_format($totalPrice, 0, ',', '.') }}</h4> {{-- Total harga semua item di keranjang --}}
												<h4 class="h5 text-white">IDR.3.000</h4> {{-- Contoh biaya tambahan lainnya --}}
												<br>
												<h4 class="h5 text-white">IDR. {{ number_format($totalPrice + 3000, 0, ',', '.') }}</h4> {{-- Total harga semua item di keranjang plus biaya tambahan --}}
											</div>
										</div>
										<div class="col-sm-4">
											<h4 class="h5 text-white">Email</h4>
											<div class="text-center">
												<input type="email" class="form-control" placeholder="Masukkan email Anda" id="email" name="email">
												<br>
												<button class="btn btn-white btn-lg py-3 btn-block pay-button">Bayar</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!---End Isi--->

		<!-- Start Footer Section -->
		<footer>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<p>Copyright © 2024 <a href="#">Muhammad Fazri Sidi Umar</a> Company. All rights reserved.
					</div>
				</div>
			</div>
		</footer>
		<!-- End Footer Section -->


		<script src="assets/js/bootstrap.bundle.min.js"></script>
		<script src="assets/js/tiny-slider.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
		<script type="text/javascript">
			// Menggunakan class 'pay-button' untuk menargetkan semua tombol bayar
			document.querySelectorAll('.pay-button').forEach(function(element) {
				element.addEventListener('click', function() {
					var email = document.getElementById("email").value;
					var snapToken = "{{ $snapToken }}"; // Menggunakan snap token dari controller
					// Melakukan pembayaran dengan snap token
					snap.pay(snapToken, {
						// Callback untuk penanganan sukses pembayaran
						onSuccess: function(result){
							console.log(result);
							alert('Pembayaran berhasil!');
							// Mengirim permintaan AJAX untuk mengirim email
							sendEmail(email);
							// Memindahkan data ke perpustakaan setelah pembayaran berhasil
							moveDataToLibrary();
						},
						// Optional: Callback untuk penanganan pending pembayaran
						onPending: function(result){
							console.log(result);
							alert('Pembayaran pending.');
						},
						// Optional: Callback untuk penanganan error pembayaran
						onError: function(result){
							console.log(result);
							alert('Terjadi kesalahan saat pembayaran.');
						}
					});
				});
			});

			// Fungsi untuk mengirim email menggunakan AJAX
			function sendEmail(email) {
				fetch('/send-email', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
					body: JSON.stringify({ email: email })
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Failed to send email');
					}
					return response.json();
				})
				.then(data => {
					console.log(data);
					// Redirect ke halaman detailtrans jika email berhasil dikirim
					window.location.href = "{{ route('landing.detailtrans') }}";
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Error sending email: ' + error.message);
				});
			}

			// Fungsi untuk memindahkan data dari keranjang belanja ke perpustakaan pengguna
			function moveDataToLibrary() {
				var gameIds = []; // Inisialisasi array untuk menyimpan game_id
				
				// Mengambil semua elemen dengan class .game_id
				var gameIdsElements = document.querySelectorAll('.game_id');
				
				// Iterasi setiap elemen dan tambahkan nilainya ke dalam array
				gameIdsElements.forEach(function(element) {
					gameIds.push(element.value); // Tambahkan nilai game_id ke dalam array
				});
				
				// Pastikan gameIds tidak kosong
				if (gameIds.length === 0) {
					console.error('No game IDs found.');
					alert('Error: No game IDs found.');
					return;
				}

				// Lakukan fetch ke endpoint untuk memindahkan data ke perpustakaan
				fetch('{{ route("moveToLibrary") }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: JSON.stringify({ game_ids: gameIds }) // Mengirim array game_ids ke server
				})
				.then(response => {
					if (!response.ok) {
						throw new Error('Failed to move data to library');
					}
					return response.json();
				})
				.then(data => {
					console.log(data);
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Error moving data to library: ' + error.message);
				});
			}
		</script>

	</body>

</html>
