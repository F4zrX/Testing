<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>Cyborg - Awesome HTML5 Template</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
<!--

TemplateMo 579 Cyborg Gaming

https://templatemo.com/tm-579-cyborg-gaming

-->
  </head>

<body>

  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->

  <!-- ***** Header Area Start ***** -->
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
                        <li><a href="{{ route('landing.index') }}">Home</a></li>
                        <li><a href="{{ route('landing.browse') }}">Browse</a></li>
                        @auth
                            <li><a href="{{ route('landing.cart', ['id' => Auth::id()]) }}">Cart</a></li>
                        @else
                            <li><a href="{{ route('landing.history') }}">Cart</a></li>
                        @endauth
                        <li><a href="{{ route('landing.wishlist') }}">Wishlist</a></li>
                        <li>
                            <a href="{{ route('landing.profile') }}" class="active">
                                Profile 
                                @if(Auth::user()->image)
                                    <img src="{{ asset('storage/user/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="img-fluid rounded-circle" >
                                @else
                                    <img src="{{ asset('assets/images/profile.jpg') }}" alt="Default Profile Image">
                                @endif
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
  <!-- ***** Header Area End ***** -->

  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-content">

          <!-- ***** Banner Start ***** -->
          <div class="row">
            <div class="col-lg-12">
              <div class="main-profile ">
                <div class="row">
                  <div class="col-lg-4">
                  @if (Auth::user()->image == null)
                  <img src="assets/images/profile.jpg" alt="" style="border-radius: 23px; width: 300px; height: 300px;">
                   @else
                  <img class="photo" src="{{ asset('storage/user/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" style="border-radius: 23px; width: 300px; height: 300px;">
                  @endif
                  </div>
                  <div class="col-lg-4 align-self-center">
                  @if(session('success'))
                      <div class="alert alert-success mt-3">
                          {{ session('success') }}
                      </div>
                  @endif
                    <div class="main-info header-text">
                      @auth
                      <h4>{{ Auth::user()->name }}</h4>
                      <p>Selamat datang dan selamat berbelanja di Code Craze Mart</p>
                      <div class="main-border-button">
                        <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
                        @csrf
                            @method('patch')
                          <input type="file" name="image">
                          <br>
                          <br>
                          <button type="submit" class="btn btn-outline-light mt-auto">Update Profile</button>
                        </form>
                      </div>
                      @endauth
                    </div>
                  </div>
                  <!-- Tampilkan pesan flash -->
                  <div class="col-lg-4 row align-items-center">
                    <ul>
                      <h2 class="h5 text-white ">Total Pengeluaran</h2>
                      <br>
                      <li>IDR. {{ number_format($totalPengeluaran) }}</li>
                    </ul>
                  </div>
                  <form action="{{ route('logout') }}" method="post">
                    <br>
                      @csrf {{-- Tambahkan csrf token untuk keamanan --}}
                      <button type="submit" class="btn btn-outline-light mt-auto">Log-Out</button>
                  </form>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="clips">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="heading-section">
                            <h4><em></em> History</h4>
                          </div>
                        </div>
                        <!-- Mengambil data Transaksi -->
                        <div class="after">
                            <!-- Tambahkan event onclick untuk setiap button -->
                            <input type="button" value="Belum dibayar" class="btn btn-outline-light mt-auto" onclick="filterItems('BELUM_DIBAYAR')">
                            <input type="button" value="Dibayar" class="btn btn-outline-light mt-auto" onclick="filterItems('DIBAYAR')">
                            <input type="button" value="Terkirim" class="btn btn-outline-light mt-auto" onclick="filterItems('TERKIRIM')">
                            <input type="button" value="Selesai" class="btn btn-outline-light mt-auto" onclick="filterItems('SELESAI')">
                        </div>

                        <br>
                        <br>

                        <div class="col-lg-12">
                            <ul class="list-group list-group-black" id="listBarang">
                                @foreach($libraries as $library)
                                    <li class="list-group-item" data-status="{{ $library->status }}">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="his">
                                                    <img src="{{ asset('storage/image_data/' . $library->game->image) }}" alt="{{ $library->game->nama }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="down-content">
                                                    <h4>{{ $library->game->nama }}</h4>
                                                    <p><i class="fa fa-calendar"></i> {{ $library->created_at->format('d/m/Y') }}</p>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <!-- Periksa data-target modal -->
                                                    <button class="btn btn-outline-light mt-auto" onclick="showModal('{{ $library->id }}', '{{ $library->game->nama }}', '{{ $library->created_at->format('d/m/Y H:i:s') }}', '{{ $library->order_id }}', '{{ $library->game->image }}', '{{ $library->qty }}', '{{ $library->harga }}', '{{ $library->total_harga }}', '{{ $library->code }}', '{{ auth()->user()->name }}', '{{ auth()->user()->email }}')">Detail</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="status">
                                                    @if($library->status == \App\Enums\LibraryStatus::BELUM_DIBAYAR)
                                                        <span class="badge badge-warning">{{ \App\Enums\LibraryStatus::BELUM_DIBAYAR }}</span>
                                                    @elseif($library->status == \App\Enums\LibraryStatus::DIBAYAR)
                                                        <span class="badge badge-primary">{{ \App\Enums\LibraryStatus::DIBAYAR }}</span>
                                                    @elseif($library->status == \App\Enums\LibraryStatus::TERKIRIM)
                                                        <span class="badge badge-info">{{ \App\Enums\LibraryStatus::TERKIRIM }}</span>
                                                    @elseif($library->status == \App\Enums\LibraryStatus::SELESAI)
                                                        <span class="badge badge-success">{{ \App\Enums\LibraryStatus::SELESAI }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel">Detail Pembelian</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Konten modal diisi oleh JavaScript -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Beli Lagi</button>
                                        <button type="button" class="btn btn-info" onclick="sendEmail()">Bantuan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ***** Banner End ***** -->
        </div>
      </div>
    </div>
  </div>
  
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © 2036 <a href="#">Cyborg Gaming</a> Company. All rights reserved. 
          
          <br>Design: <a href="https://templatemo.com" target="_blank" title="free CSS templates">TemplateMo</a></p>
        </div>
      </div>
    </div>
  </footer>


  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  </body>
  <script>
      // Function to show modal and populate it with data
      function showModal(libraryId, gameNama, tanggalPembelian, orderID, gambar, qty, harga, totalHarga, code, namaUser, emailUser) {
          $('#detailModal').modal('show');
          $('#detailModal .modal-body').html(`
              <p><strong>Order ID:</strong> ${orderID}</p>
              <p><strong>Tanggal Pembelian:</strong> ${tanggalPembelian}</p>
              <hr>
              <h5>Detail Produk:</h5>
              <div class="row">
                  <div class="col-lg-3">
                      <img src="{{ asset('storage/image_data/') }}/${gambar}" alt="${gameNama}" class="img-fluid">
                  </div>
                  <div class="col-lg-9">
                      <p><strong>Nama Produk:</strong> ${gameNama}</p>
                      <p><strong>Qty:</strong> ${qty}</p>
                      <p><strong>Harga:</strong> ${harga}</p>
                      <p><strong>Total Harga:</strong> ${totalHarga}</p>
                  </div>
              </div>
              <hr>
              <p><strong>Code:</strong> ${code}</p>
              <p><strong>Nama :</strong> ${namaUser}</p>
              <p><strong>Email:</strong> ${emailUser}</p>
          `);
      }
  </script>
  <script>
      function sendEmail() {
          // Definisikan email tujuan
          var emailTujuan = 'codecrazemart@gmail.com';
          // Definisikan subject email
          var subject = 'Pertanyaan/Pesan untuk Codecraze Mart';

          // Buat link email
          var link = 'mailto:' + emailTujuan + '?subject=' + subject;

          // Redirect ke halaman email dengan email tujuan dan subject yang sudah terisi
          window.location.href = link;
      }
  </script>
  <script>
      function filterItems(status) {
          // Ambil semua item list barang
          var items = document.querySelectorAll('#listBarang li');

          // Loop melalui setiap item
          items.forEach(function(item) {
              // Ambil status dari atribut data-status
              var itemStatus = item.getAttribute('data-status');

              // Jika status item sesuai dengan status yang dipilih
              if (itemStatus === status) {
                  // Tampilkan item
                  item.style.display = 'block';
              } else {
                  // Sembunyikan item
                  item.style.display = 'none';
              }
          });
      }
  </script>


</html>
