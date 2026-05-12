<?= $this->extend('templatetes/admin') ?>
<?= $this->section('content') ?>
<!-- Begin Page Content -->
<style>
    .placeholder {
      background-color: #e0e0e0;
      border-radius: 4px;
      animation: placeholder-loading 1.2s infinite;
      height: 20px;
    }
    
    @keyframes placeholder-loading {
      0% {
        background-color: #e0e0e0;
      }
      50% {
        background-color: #f0f0f0;
      }
      100% {
        background-color: #e0e0e0;
      }
    }
    
    .hidden {
      display: none;
    }

  .slider-wrapper {
    position: relative;
  }
  .slider-container {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 10px;
  }
  .slider-container::-webkit-scrollbar {
    display: none; /* sembunyikan scrollbar */
  }
  @media only screen and (max-width: 767px) {
      .product-card {
        flex: 0 0 auto;
        width: 70%;
        margin-right: 15px;
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px;
        border-radius: 8px;
      }
  }
  
  @media only screen and (min-width: 767px) {
      .product-card {
        flex: 0 0 auto;
        width: 50%;
        margin-right: 15px;
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px;
        border-radius: 8px;
      }
  }
  
  .product-card img {
    width: 100%;
    height: auto;
  }
  .slider-btn {
    position: absolute;
    top: 40%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 2;
  }
  .slider-btn.prev {
    left: -20px;
  }
  .slider-btn.next {
    right: -20px;
  }
   .title-event{
      color:#212121;
  }
  .card-text{
      font-size:12px;
  }
</style>
<div class="container-fluid">

  <!-- Page Heading -->

  <!-- Content Row -->
  <?php
  $pesan = session()->getFlashData('pesan');
  if (!empty($pesan)) {
    echo $pesan;
  } ?>
    
    <ul class="nav nav-tabs sticky-top bg-light" style="z-index: 2;" id="myTab" role="tablist">
        <li class=" d-flex align-items-center">
            <a class="nav-link rounded-0" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</a>
        </li> 
        <li class=" d-flex align-items-center">
            <a class="nav-link rounded-0 active" id="berita-tab" data-toggle="tab" href="#berita" role="tab" aria-controls="berita" aria-selected="false">Berita</a>
        </li>
    </ul>
    <div class="tab-content hidden" id="myTabContent">
        <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
            <div class="row mt-4">
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Penjualan Tahun Ini</div>
                          <div class="h5 mb-0 mt-2 font-weight-bold text-gray-800" style="font-size: 16px;">Rp. <?php echo ($jlhpenjualan >= 0 ? number_format($jlhpenjualan) : "(" . number_format($jlhpenjualan * -1) . ")") ?></div>
                        </div>
                        <div class="col-auto">
                          <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laba Rugi Tahun Ini</div>
                          <div class="h5 mb-0 mt-2 font-weight-bold text-gray-800" style="font-size: 16px;">Rp. <?php echo ($jlhlabarugi >= 0 ? (number_format($jlhlabarugi)) : "(" . number_format($jlhlabarugi * -1) . ")") ?></div>
                        </div>
                        <div class="col-auto">
                          <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Piutang Belum Dibayar</div>
                          <div class="h5 mb-0 mt-2 mr-3 font-weight-bold text-gray-800" style="font-size: 16px;">Rp. <?php echo ($jlhpiutang >= 0 ? number_format($jlhpiutang) : "(" . number_format($jlhpiutang * -1) . ")") ?></div>
                        </div>
                        <div class="col-auto">
                          <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            
                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                      <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Hutang Belum Dibayar</div>
                          <div class="h5 mb-0 mt-2 font-weight-bold text-gray-800" style="font-size: 16px;">Rp. <?php echo ($jlhhutang >= 0 ? number_format($jlhhutang) : "(" . number_format($jlhhutang * -1) . ")") ?></div>
                        </div>
                        <div class="col-auto">
                          <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                  <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-success">Grafik Penjualan</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                      <div class="row p-0">
                        <div class="col-md-4 p-0">
                          <select name="mingguan" id="mingguan" class="form-control form-control-sm">
                            <option value="0">Mingguan</option>
                            <option value="1">Minggu I</option>
                            <option value="2">Minggu II</option>
                            <option value="3">Minggu III</option>
                            <option value="4">Minggu IV</option>
                            <option value="5">Minggu V</option>
                          </select>
                        </div>
                        <div class="col-md-4 p-0">
                          <select name="bulanan" id="bulanan" class="form-control form-control-sm">
                            <option value="0">Bulanan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                          </select>
                        </div>
                        <div class="col-md-3 p-0">
                          <select name="tahunan" id="tahunan" class="form-control form-control-sm">
                            <?php
                            $tahuawal = date('Y') - 5;
                            $tahunakhir = date('Y');
            
                            for ($i = $tahuawal; $i <= $tahunakhir; $i++) {
                              $selected = '';
                              if ($i == date('Y')) {
                                $selected = 'selected="selected"';
                              }
                              echo '
                                            <option value="' . $i . '" ' . $selected . '>' . $i . '</option>
                                          ';
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-1 p-0">
                          <button><i class="fa fa-search" id="refresh_grafik_penjualan"></i></button>
                        </div>
                        <div class="col-md-12">
                          <hr>
                        </div>
                      </div>
                      <div class="chart-area">
                        <canvas id="line-chart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
            
            
                <div class="col-xl-6">
                  <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-warning">Grafik Biaya</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                      <div class="row p-0">
                        <div class="col-md-4 p-0">
                          <select name="mingguan2" id="mingguan2" class="form-control form-control-sm">
                            <option value="0">Mingguan</option>
                            <option value="1">Minggu I</option>
                            <option value="2">Minggu II</option>
                            <option value="3">Minggu III</option>
                            <option value="4">Minggu IV</option>
                            <option value="5">Minggu V</option>
                          </select>
                        </div>
                        <div class="col-md-4 p-0">
                          <select name="bulanan2" id="bulanan2" class="form-control form-control-sm">
                            <option value="0">Bulanan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                          </select>
                        </div>
                        <div class="col-md-3 p-0">
                          <select name="tahunan2" id="tahunan2" class="form-control form-control-sm">
                            <?php
                            $tahuawal = date('Y') - 5;
                            $tahunakhir = date('Y');
            
                            for ($i = $tahuawal; $i <= $tahunakhir; $i++) {
                              $selected = '';
                              if ($i == date('Y')) {
                                $selected = 'selected="selected"';
                              }
                              echo '
                                            <option value="' . $i . '" ' . $selected . '>' . $i . '</option>
                                          ';
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-1 p-0">
                          <button><i class="fa fa-search" id="refresh_grafik_biaya"></i></button>
                        </div>
                        <div class="col-md-12">
                          <hr>
                        </div>
                      </div>
                      <div class="chart-area">
                        <canvas id="line-chart2"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade show active" id="berita" role="tabpanel" aria-labelledby="berita-tab">
            <div class="mt-4">
                <div class="row">
                    <div class="col-md-8 mt-2">
                        <div class="bd-example">
                            <div id="banner-left" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <?php $nokiriindikator = 0;
                                        foreach($banner as $no=>$rows): ?>
                                            <?php if($rows->status == 'banner-kiri'): ?>
                                                <li data-target="#banner-left" data-slide-to="<?= $nokiriindikator ?>" class="<?= $nokiriindikator == '0'? 'active':'' ?>"></li>
                                            <?php $nokiriindikator++; ?>
                                            <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                                <div class="carousel-inner">
                                    <?php $nokiri=0; foreach($banner as $rows): ?>
                                        <?php if($rows->status == 'banner-kiri'): ?>
                                            <div class="carousel-item <?= $nokiri == '0'? 'active':'' ?>">
                                                <a href="<?= $rows->url ?>" target="_blank">
                                                    <img src="<?= base_url('uploads/iklan/thumbnails/'.$rows->file) ?>" class="d-block w-100 rounded" alt="<?= $rows->nama_iklan ?>">
                                                </a>
                                            </div>
                                        <?php $nokiri++; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="bd-example">
                            <div id="banner-righ" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <?php $noright=0; foreach($banner as $rows): ?>
                                        <?php if($rows->status == 'banner-kanan'): ?>
                                            <div class="carousel-item <?= $noright == '0'? 'active':'' ?>">
                                                <a href="<?= $rows->url ?>" target="_blank">
                                                    <img src="<?= base_url('uploads/iklan/thumbnails/'.$rows->file) ?>" class="d-block w-100 rounded" alt="<?= $rows->nama_iklan ?>">
                                                </a>
                                            </div>
                                            <?php $noright++; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-4">
                    <div class="bd-example">
                        <div id="banner-full" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <?php $nofull=0; foreach($banner as $rows): ?>
                                    <?php if($rows->status == 'banner-full'): ?>
                                        <div class="carousel-item <?= $nofull == '0'? 'active':'' ?>">
                                            <a href="<?= $rows->url ?>" target="_blank">
                                                <img src="<?= base_url('uploads/iklan/thumbnails/'.$rows->file) ?>" class="d-block w-100 rounded" alt="<?= $rows->nama_iklan ?>">
                                            </a>
                                        </div>
                                        <?php $nofull++; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="my-5">
                    <h5 class="fw-bold title-event">Event Terbaru</h5>
                    <div class="slider-wrapper">
                        <button class="slider-btn prev" id="prevBtn">&#10094;</button>
                        <div class="slider-container" id="productSlider">
                            <?php foreach ($event as $item): ?>
                                <div class="product-card">
                                    <img src="<?= base_url('uploads/event/thumbnails/'.$item->file) ?>" class="card-img-top" alt="<?= $item->nama_event ?>">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark"><?= $item->nama_event ?></h6>
                                        <p class="card-text"><?= $item->deskripsi ?></p>
                                        <a href="<?= $item->url ?>" class="btn btn-primary btn-block">Daftar Event</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="slider-btn next" id="nextBtn">&#10095;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--//untuk loading-->
    <div id="loading" class="mt-4">
        <div class="mb-3">
          <div class="placeholder w-100 mb-2" style="height: 150px;"></div>
        </div>
        <div class="mb-3">
          <div class="placeholder w-75 mb-2"></div>
          <div class="placeholder w-50"></div>
        </div>
    </div>
  
</div>
<!-- /.container-fluid -->

<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css">-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>-->

<script>
  $(document).ready(function() {
    load_grafik_penjualan();
    load_grafik_biaya();
  });


  $('#refresh_grafik_biaya').click(function() {
    load_grafik_biaya();
  });

  $('#refresh_grafik_penjualan').click(function() {
    load_grafik_penjualan();
  });

  function load_grafik_penjualan() {
    var mingguan = $('#mingguan').val();
    var bulanan = $('#bulanan').val();
    var tahunan = $('#tahunan').val();

    formData = {
      'mingguan': mingguan,
      'bulanan': bulanan,
      'tahunan': tahunan,
    }

    $.ajax({
        type: 'POST',
        url: '<?php echo (site_url('Dashboard/get_grafik_penjualan')) ?>',
        data: formData,
        dataType: 'json',
        encode: true
      })
      .done(function(result) {

        if (result.success) {

          new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
              labels: result.labels,
              datasets: [{
                  data: result.data_lsm,
                  label: "Penjualan",
                  borderColor: "#1cc88a",
                  fill: false
                },

              ]
            },
            options: {
              title: {
                display: true,
                text: ' ( N / Juta ) Rupiah'
              }
            }
          });

        } else {
          console.log('test');
          alert(result.msg);
        }



      });
  }

  function load_grafik_biaya() {
    var mingguan = $('#mingguan2').val();
    var bulanan = $('#bulanan2').val();
    var tahunan = $('#tahunan2').val();

    formData = {
      'mingguan': mingguan,
      'bulanan': bulanan,
      'tahunan': tahunan,
    }

    $.ajax({
        type: 'POST',
        url: '<?php echo (site_url('Dashboard/get_grafik_biaya')) ?>',
        data: formData,
        dataType: 'json',
        encode: true
      })
      .done(function(result) {

        if (result.success) {

          new Chart(document.getElementById("line-chart2"), {
            type: 'line',
            data: {
              labels: result.labels,
              datasets: [{
                data: result.data_biaya,
                label: "Pengeluaran",
                borderColor: "#f6c23e",
                fill: false
              }]
            },
            options: {
              title: {
                display: true,
                text: ' ( N / Juta ) Rupiah'
              }
            }
          });

        } else {
          console.log('test');
          alert(result.msg);
        }



      });
  }
  
    // setTimeout(function () {
    //     document.getElementById('loading').classList.add('hidden');
    //     document.getElementById('myTabContent').classList.remove('hidden');
    // }, 3000); // 3000ms = 3 detik
</script>
<script>
    // Scroll pakai tombol
    const slider = document.getElementById('productSlider');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    nextBtn.addEventListener('click', () => {
      slider.scrollBy({ left: 220, behavior: 'smooth' });
    });
    prevBtn.addEventListener('click', () => {
      slider.scrollBy({ left: -220, behavior: 'smooth' });
    });
    
    // Geser slider dengan drag mouse
    let isDown = false;
    let startX;
    let scrollLeft;
    
    slider.addEventListener('mousedown', (e) => {
      isDown = true;
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });
    slider.addEventListener('mouseleave', () => {
      isDown = false;
    });
    slider.addEventListener('mouseup', () => {
      isDown = false;
    });
    slider.addEventListener('mousemove', (e) => {
      if(!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 1; 
      slider.scrollLeft = scrollLeft - walk;
    });
</script>
<?= $this->endSection() ?>