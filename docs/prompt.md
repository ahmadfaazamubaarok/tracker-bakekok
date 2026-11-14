aku mau membuat proyek web untuk tracking progress proyekku, berikut adalah user flownya

user masuk ke dashboard (Board page) tanpa login dan langsung disajikan kanban board yang berisi 3 kolom status progress, yaitu pending, wip, dan selesai.
jika user belum pernah membuat project sebelumnya, maka ditampilkan dahulu tombol pindah ke halaman project untuk menambahkan minimal 1 project. 
saat masuk ke halaman projects, user melihat daftar project, di sana ia bisa melakukan CRUD project.
Jika user belum memiliki group project maka di halaman project akan ditampilkan terlebih dahulu tombol yang mengarahkan ke halaman group.
user masuk ke halaman group lalu menambahkan minimal 1 group project.
jadi kebutuhan dari dasar adalah:
- grup
- proyek
- progres
- arsip
user bisa menambahkan progress baru saat data yang sebelumnya sudah lengkap minimal 1, karena sebagai patokan. setiap progress memiliki 1 project yang memiliki 1 group.

berikut adalah isian setiap bagian:

GRUP
- nama

PROYEK
- nama
- deskripsi (opsional)
- group (pilih yang sudah ada)
- alamat domain
- alamat direktori
- warna

PROGRES
- nama
- proyek (pilih yang sudah ada)
- deadline
- deskripsi (opsional)
- prioritas (rendah, sedang, tinggi)

setelah user selesai menambahkan progress, otomatis progress tersebut akan ditempatkan di kolom pending. user bisa sewaktu2 menggeser dengan drag drop card progress ke kolom wip maupun done. setelah progres card sudah ditaruh di kolom selesai, user bisa mengarsipkan progres tersebut. kalo user mau delete tinggal klik tombol x di setiap data nya.

tech stack di sini menggunakan Codeigniter3, Docker, mysql, nginx.
bantu aku buatkan prd nya dan segala kebutuhannya untuk aku kirimkan ke agent ai lainnya untuk mengerjakan proyek ini



--- 
# Metode Penulisan
# Model
Nama File : `Model_resource.php`

> kata `Model` harus di depan diikuti nama `resource` dan huruf depan harus Kapital
> 

Nama method:

- `list_resource()` : mengambil data resource jamak (return `result()`)
- `single_resource()` : mengambil data resource tunggal (return `row()`)
- `add_resource()` : menyimpan data resource
- `update_resource()` : mengubah data resource
- `delete_resource()` : menghapus data resource

Jika suatu kondisi ada pengambilan data yang mana ia tidak berdasarkan dengan defaultnya, misal:

> 
> 
> 
> Mau mengambil data `barang` yang berdasarkan `status` , sedangkan tidak ada method yang menangani pengambilan berdasarkan `status` , maka lihat apakah yang diambil berupa `result()` atau `row()` .
> 
> Jika yang diambil `result()` maka gunakan method `list_resource($parameter)` 
> 
> Jika yang diambil `row()` maka gunakan method `single_resource($parameter)` 
> 
> Lalu tambahkan percabangan `if($parameter != ''){ //proses yang mau diambil }` 
> 

Contoh:

```php
public function list_barang($status='')
{
	if($status != '') $this->db->where('status_barang',$status);
	//percabangan di atas adalah kondisi tambahan jika mau menambahkan kondisi selain default cara pengambilan
	$this->db->order_by('nama_barang','ASC');
	return $this->db->get('barang')->result();
}

//Jangan gunakan cara ini, karena boros
public function list_barang_by_status($status)
{
	$this->db->where('status_barang',$status);
	$this->db->order_by('nama_barang','ASC');
	return $this->db->get('barang')->result();
}
```

### Penting!

Jika suatu method di model memiliki 2 atau lebih parameter, maka pemanggilan method model juga harus melengkapi semua parameternya

```php
//Method Model memiliki 2 parameter
public function prefix_resource($param1='',$param2='')
{
//Proses..
}

//Di Controller memanggil method model
$this->Model_resource->prefix_resource($param1, $param2);
```

Urutan parameter harus tepat!

## Tambahan

Tambahkan baris pada baris terakhir setiap method `edit` dan `delete`

```jsx
$this->db->reset_query();
//agar tidak ter/mempengaruhi proses query lain
```

# Contoh Halaman

Contoh Model:

```bash
<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kategori extends CI_Model
{
	public function list_kategori()
	{
		$this->db->order_by('kategori','ASC');
		return $this->db->get('kategori')->result();
	}

	public function single_kategori($id_kategori)
	{
		$this->db->where('id_kategori',$id_kategori);
		return $this->db->get('kategori')->row();
	}

	public function add_kategori($kategori)
	{
		return $this->db->insert('kategori',$kategori);
	}

	public function update_kategori($id_kategori, $kategori)
	{
		$this->db->where('id_kategori',$id_kategori);
		$status = $this->db->update('kategori',$kategori);
		$this->db->reset_query();
		return $status;
	}

	public function delete_kategori($id_kategori)
	{
		$this->db->where('id_kategori', $id_kategori);
		$this->db->delete('kategori');
		$this->db->reset_query();
	}

	// PIVOT ARTIKEL_KATEGORI
	public function count_artikel_kategori($id_kategori)
	{
		$this->db->where('id_kategori',$id_kategori);
		return $this->db->count_all_results('artikel_kategori');
	}

	public function list_artikel_kategori($id_artikel)
	{
		$this->db->select('kategori.id_kategori, kategori.kategori');
		$this->db->from('artikel_kategori');
		$this->db->join('kategori', 'kategori.id_kategori = artikel_kategori.id_kategori');
		$this->db->where('artikel_kategori.id_artikel', $id_artikel);
		$this->db->order_by('kategori.kategori','ASC');
		return $this->db->get()->result();
	}

	public function add_artikel_kategori($data)
	{
		return $this->db->insert('artikel_kategori', $data);
	}

	public function delete_artikel_kategori($id_artikel = '', $id_kategori = '')
	{
		if ($id_artikel != '') {
			$this->db->where('id_artikel', $id_artikel);
		}
		if ($id_kategori != '') {
			$this->db->where('id_kategori', $id_kategori);
		}
		$this->db->delete('artikel_kategori');
		$this->db->reset_query();
	}
}

```

# Controller
Nama File : `Resource.php` 

> langsung nama `Resource` dengan huruf depan Kapital
> 

Nama method:

- `index()` : default method dan untuk menampilkan layout utama halaman
- `resource_daftar()` : load data resource yang akan ditampilkan di halaman utama (bisa untuk menampilkan html atau load json saja untuk tabel)
- `resource_add()` : memanggil view form add
- `resource_edit()` : memanggil view form edit
- `resource_addsave()` : memproses form add
- `resource_editsave()` : memproses form edit
- `resource_delete()` : memproses delete

## Tambahan

JSON  output

```jsx
$this->set_output([
	'status' => '**sukses**/**gagal**',
	'pesan' => '**Gagal**/**Berhasil** memproses data'
]);

// function untuk JSON output
private function set_output($data, $status=200)
	{
		$this->output
			->set_status_header($status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}
	
// Tidak usah tambahkan else blok pada output JSON
```

Array

```jsx
//Gunakan cara ini
$var = array(
	'key1' => value1,
	'key2' => value2
);
// untuk data berdependensi
$data['key1'] = $this->model_resource->method_action1($param);
$data['key2'] = $this->model_resource->method_action2($param);

// ======================================================================
//Bukan yang ini
$var = [
	'key1' => value1,
	'key2' => value2
];
// untuk data berdependensi
$var = [
	'key1' => $this->model_resource->method_action1($param),
	'key2' => $this->model_resource->method_action2($param)
];
```

# Contoh Halaman

Contoh Controller:

```bash
<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}

		$this->load->model('model_kategori');
	}

	private function set_output($data, $status=200)
	{
		$this->output
			->set_status_header($status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_PRETTY_PRINT))
			->_display();
		exit;
	}

	public function index()
	{
		$data['navbar'] = 'kategori';
		$this->load->view('kategori/kategori_view',$data);
	}

	public function kategori_daftar()
	{
		$daftar_kategori = $this->model_kategori->list_kategori();
		$daftar_input = array();

		foreach ($daftar_kategori as $key) {

			$bahan_input = array(
				$key->kategori,
				$this->model_kategori->count_artikel_kategori($key->id_kategori),
				'
				<div class="dropdown">
					<button class="btn btn-outline-secondary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item btn-kategori-edit" data-id="'.$key->id_kategori.'" href="javascript:;">Edit</a>
						<a class="dropdown-item btn-kategori-delete" data-id="'.$key->id_kategori.'" href="javascript:;">Delete</a>
					</div>
				</div>',
			);

			array_push($daftar_input, $bahan_input);
		}

		$response = array(
			'data' => $daftar_input
		);
		$this->set_output($response);
	}

	public function kategori_add()
	{
		$this->load->view('kategori/kategori_add');
	}

	public function kategori_addsave()
	{
		$kategori = $this->input->post('kategori');

		if (trim($kategori) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama kategori tidak boleh kosong'
			), 400);
		}

		$bahan = array(
			'id_kategori' => uniqid(),
			'kategori' => trim($kategori)
		);

		if ($this->model_kategori->add_kategori($bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Berhasil menambahkan kategori ' . $bahan['kategori']
			));
		} else {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Gagal menambahkan kategori'
			), 500);
		}
	}

	public function kategori_edit($id_kategori = '')
	{
		$data['kategori'] = $this->model_kategori->single_kategori($id_kategori);
		$this->load->view('kategori/kategori_edit',$data);
	}

	public function kategori_editsave()
	{
		$id_kategori = $this->input->post('id_kategori');
		$kategori = $this->input->post('kategori');

		if (trim($kategori) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama kategori tidak boleh kosong'
			), 400);
		}

		$bahan = array('kategori' => trim($kategori));

		if ($this->model_kategori->update_kategori($id_kategori, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Berhasil mengubah kategori'
			));
		} else {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Gagal mengubah kategori'
			), 500);
		}
	}

	public function kategori_delete()
	{
		$id_kategori = $this->input->post('id_kategori');

		$this->db->trans_start();
		$this->model_kategori->delete_artikel_kategori_by_kategori($id_kategori);
		$this->model_kategori->delete_kategori($id_kategori);
		$this->db->trans_complete();

		if ($this->db->trans_status() == TRUE) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Berhasil menghapus kategori'
			));
		} else {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Gagal menghapus kategori'
			), 500);
		}
	}
}

```

# View
Nama File :

- `resource_view.php` : menampilkan layout utama halaman
- `resource_daftar.php` : menampilkan data resource menggunakan fragmen html yang diload modular
- `resource_detail.php` : menampilkan fragmen html detail resource
- `resource_add.php` : menampilkan fragmen html form add (untuk konten modal)
- `resource_edit.php` : menampilkan fragmen html form edit (untuk konten modal)

Jika pada suatu kondisi sebuah resource memiliki informasi tambahan yang masih terkait pada fitur tersebut maka ditambahkan, contoh pada penerapan resource `kategori` :

View/`kategori`:

- `kategori/kategori_view.php`(asli)
- `kategori_daftar.php` (asli)
- `kategori_add.php`(asli)
- `kategori_edit.php` (asli)
- `kategori_prioritas_add.php` (tambahan)
- `kategori_prioritas_edit.php` (tambahan)

Bisa ditulis tanpa prefix `kategori_` jika `prioritas` tidak digunakan pada fitur lain, sehingga ditulis menjadi:

- `prioritas_add.php` (tambahan)
- `prioritas_edit.php` (tambahan)

---

Jika tambahan yang kedua digunakan pada fitur lain, contoh pada fitur `guru` yang mana ia memiliki `kelas`, lalu ada fitur lain yaitu `wali` yang ia juga memiliki `kelas` , maka dilarang menghilangkan prefix depannya (resource sumber) untuk informasi tambahan:

**Guru:**

- `guru_view.php` (asli)
- `guru_detail.php` (asli)
- `guru_add.php` (asli)
- `guru_edit.php` (asli)
- `guru_kelas_view.php` (tambahan)
- `guru_kelas_detail.php` (tambahan)
- `guru_kelas_add.php` (tambahan)
- `guru_kelas_edit.php` (tambahan)

**Wali:**

- `wali_view.php` (asli)
- `wali_detail.php` (asli)
- `wali_add.php` (asli)
- `wali_edit.php` (asli)
- `wali_kelas_view.php` (tambahan)
- `wali_kelas_detail.php` (tambahan)
- `wali_kelas_add.php` (tambahan)
- `wali_kelas_edit.php` (tambahan)

## Tambahan

Gunakan petik 2 `“”` untuk javascript

Gunakan petik 1 `‘’` untuk php

```jsx
// Penulisan URL pada ajax dibuat statis saja
ajax: "<?= site_url('settings/users_daftar'); ?>"
// Menjadi
ajax: "/settings/user_daftar"
```

Penamaan `class` dan `id`:

```html
<!-- Tombol aksi -->
<a href="javascript:;" class="btn-user-add"></a>
<a href="javascript:;" class="btn-user-edit"></a>
<a href="javascript:;" class="btn-user-delete"></a>

<!-- Formulir -->
<form id="form-user-add">
<form id="form-user-edit">

```

## Template Layout

`templates/0_head.php` : berisi tag html buka hingga head, lengkap dengan link css yang dibutuhkan, tag body dibuka di sini

`templates/1_topbar.php` : berisi navbar dan container induk halaman

`templates/2_footer.php` : berisi footer section dan link script dan tutup container induk halaman

`templates/3_end.php` : berisi tutup body dan html

---

# Contoh Halaman

Contoh halaman `templates/0_head.php`:

```bash
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Portal</title>
	<!-- vendor css -->
	<link href="<?= base_url() ?>cdn/lib/@fontawesome/fontawesome-pro-5.8.2/css/all.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/magic-check/magic-check.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/select2/css/select2.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/jqueryui/jquery-ui.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>cdn/lib/bootstrap-toastr/toastr.min.css" rel="stylesheet">

	<!-- DashForge CSS -->
	<link rel="stylesheet" href="<?= base_url() ?>cdn/assets/css/dashforge.css">
	<link rel="icon" type="image/png" sizes="16x16" href="https://ppasm.com/assets/images/logo/logo_assalafiyyah.png">
	<link rel="manifest" href="<?= base_url() ?>cdn/assets/favicons/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?= base_url() ?>cdn/assets/favicons/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

	<!-- some additional css here -->
	<style type="text/css">
		.navbar-brand
		{
			padding-top: 0.5rem;
		}
		.hidden
		{
			display: none;
		}
		/* Fix scrollbar horizontal: DataTables 1.10 menulis width px secara
		   inline ke tabel saat init (hasil ukur internalnya ikut menghitung
		   border dgn content-box), membuat tabel ~2-3px lebih lebar dari
		   container .table-responsive. !important mengalahkan inline style. */
		table.dataTable,
		table.dataTable th,
		table.dataTable td
		{
			box-sizing: border-box;
		}
		table.dataTable
		{
			width: 100% !important;
		}
		/* Fix warna toastr: dashforge.css ikut mendefinisikan .toast (background
		   putih) dan dimuat setelah toastr.min.css sehingga warna success/error/
		   warning tertimpa. Naikkan specificitas + !important. */
		#toast-container > .toast
		{
			background-image: none !important;
		}
		#toast-container > .toast-success
		{
			background-color: #51a351 !important;
		}
		#toast-container > .toast-error
		{
			background-color: #bd362f !important;
		}
		#toast-container > .toast-warning
		{
			background-color: #f89406 !important;
		}
		#toast-container > .toast-info
		{
			background-color: #2f96b4 !important;
		}
		#toast-container .toast-title,
		#toast-container .toast-message
		{
			color: #ffffff;
		}
		.df-logo span {
			font-weight: 700;
		}
	</style>
```

Contoh halaman `templates/1_topbar.php`:

```bash
<body class="bg-light pt-5">
	<header class="navbar navbar-header navbar-header-fixed">
		<a href="" id="mainMenuOpen" class="burger-menu"><i data-feather="menu"></i></a>
		<div class="navbar-brand">
			<a href="/" class="df-logo">portal.<span>ppasm</span></a>
		</div>
		<div id="navbarMenu" class="navbar-menu-wrapper">
			<div class="navbar-menu-header">
				<a href="/" class="df-logo">portal.<span>ppasm</span></a>
				<a id="mainMenuClose" href=""><i data-feather="x"></i></a>
			</div>
			<ul class="nav navbar-menu">
				<li class="nav-label pd-l-20 pd-lg-l-25 d-lg-none">MENU</li>
				<li class="nav-item <?= ($navbar == 'dashboard')?'active':'' ?>">
					<a href="/" class="nav-link"><i data-feather="home"></i> Dashboard</a>
				</li>
				<li class="nav-item <?= ($navbar == 'artikel')?'active':'' ?>">
					<a href="/artikel" class="nav-link"><i data-feather="credit-card"></i> Artikel</a>
				</li>
				<li class="nav-item <?= ($navbar == 'kategori')?'active':'' ?>">
					<a href="/kategori" class="nav-link"><i data-feather="tag"></i> Kategori</a>
				</li>
				<li class="nav-item <?= ($navbar == 'settings')?'active':'' ?>">
					<a href="/settings" class="nav-link"><i data-feather="credit-card"></i> Settings</a>
				</li>
			</ul>
		</div>
		<div class="navbar-right">
			<div class="dropdown dropdown-profile">
				<a href="" class="dropdown-link" data-toggle="dropdown" data-display="static">
				<div class="avatar avatar-sm">
					<img src="https://dummyimage.com/100x100/545454/fff" class="rounded-circle" alt="Ahmad Faaza Mubaarok">
				</div>
				</a><!-- dropdown-link -->
				<div class="dropdown-menu dropdown-menu-right tx-13">
					<div class="avatar avatar-lg mg-b-15"><img src="https://dummyimage.com/100x100/545454/fff" class="rounded-circle" alt="Ahmad Faaza Mubaarok"></div>
					<h6 class="tx-semibold mg-b-5"><?= $this->session->userdata('user')['username'] ?></h6>
						<a href="/settings/profil_edit" class="dropdown-item"><i data-feather="edit-3"></i> Edit Akun</a>
						<div class="dropdown-divider"></div>
					<a href="/auth/logout" class="dropdown-item"><i data-feather="log-out"></i>Logout</a>
				</div><!-- dropdown-menu -->
			</div><!-- dropdown -->
		</div>
	</header>
```

Contoh halaman t`emplates/2_footer.php`:

```bash
	<div class="mb-5"></div>
	<script src="<?= base_url() ?>cdn/lib/jquery/jquery.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/feather-icons/feather.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/jqueryui/jquery-ui.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/jquery-number/jquery.number.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/bootbox/bootbox.all.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/bootstrap-toastr/toastr.min.js"></script>
	
	<!-- <script src="<?= base_url() ?>cdn/lib/datatables.net/js/datatable-mod.js"></script> -->
	<script src="<?= base_url() ?>cdn/lib/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/select2/js/select2.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/jquery-repeater/jquery.repeater.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/jquery-autosize/autosize.min.js"></script>
	<script src="<?= base_url() ?>cdn/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>

	<script src="<?= base_url() ?>cdn/assets/js/dashforge.js"></script>
```

Contoh halaman `templates/3_end.php`:

```bash
</body>
</html>
```

Contoh halaman `resource/resouce_view.php`:

```bash
<?php $this->load->view('templates/0_header') ?>
<?php $this->load->view('templates/1_topbar') ?>
<style>
#tabel-kategori_length select {
    display: inline-block;
    width: auto;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    outline: none;
    box-shadow: none;
}

#tabel-kategori_length select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
#tabel-kategori_wrapper .dataTables_length,
#tabel-kategori_wrapper .dataTables_paginate {
    margin: 0;
    padding: 0;
}

#tabel-kategori_wrapper .dataTables_length {
    display: flex;
    align-items: center;
    gap: 8px;
}

#tabel-kategori_wrapper .dataTables_length label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

#tabel-kategori_wrapper .dataTables_length select {
    margin: 0;
}

#tabel-kategori_wrapper .dataTables_paginate {
    display: flex;
    align-items: center;
    margin-left: 8px;
}

@media (max-width: 576px) {
    #tabel-kategori_wrapper .dataTables_length,
    #tabel-kategori_wrapper .dataTables_paginate {
        width: 100%;
        justify-content: center;
        margin-top: 8px;
        margin-left: 0;
    }
}
</style>
<div class="container mt-5">
	<div class="d-flex justify-content-between mt-3">
		<h3>Kategori</h3>
		<div>
			<a href="javascript:;" class="btn btn-primary btn-xs btn-kategori-add">+ Kategori</a>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered my-3 bg-white" id="tabel-kategori">
			<thead>
				<tr>
					<th>Kategori</th>
					<th>Jumlah Artikel</th>
					<th width="1%">Aksi</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr class="filter">
					<th>
						<input type="" name="" class="form-control w-100">
					</th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<div id="modal_frame" class="modal" tabindex="-1" sris-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal_dinamis">
				<div class="modal-body">
					<p>Modal body text goes here.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('templates/2_footer') ?>
<script>
$(document).ready(function(){
	let pesan_loading = "<p class='text-center'><em>Loading Bro Sabaar....</em></p>";
	let frame = $("#modal_frame");

	var tabel_kategori = $("#tabel-kategori").DataTable({
		ajax: "/kategori/kategori_daftar",
		dom: 'rt<"d-flex justify-content-end align-items-center flex-wrap mt-3"lp>',
		language: {
			lengthMenu: "_MENU_"
		},
		orderClasses:false
	});

	$(".filter input").on("keyup", function(){
		tabel_kategori
			.column($(this).closest("th").index())
			.search(this.value)
			.draw();
	});

	$(document).on("click",".btn-kategori-add",function(){
		frame.find(".modal-title").html("Tambah kategori");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");
		$.get("/kategori/kategori_add", function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("click",'.btn-kategori-edit',function(){
		frame.find(".modal-title").html("Edit kategori");
		frame.find(".modal-body").html(pesan_loading);
		frame.modal("show");

		let id_kategori = $(this).data("id");
		$.get("kategori/kategori_edit/"+id_kategori, function(res) {
			frame.find(".modal-body").html(res);
		});
	});

	$(document).on("submit","#form-kategori-add",function(e){
		e.preventDefault();
		$.ajax({
			url:"/kategori/kategori_addsave",
			type:"POST",
			data:$(this).serialize(),
			dataType:"json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_kategori.ajax.reload();
				frame.modal("hide");
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(){
			toastr.error("Terjadi error di server","Gagal");
		});
	});

	$(document).on("submit","#form-kategori-edit",function(e){
		e.preventDefault();
		$.ajax({
			url:"/kategori/kategori_editsave",
			type:"POST",
			data:$(this).serialize(),
			dataType:"json"
		})
		.done(function(respon){
			if (respon.status === "sukses") {
				toastr.success(respon.pesan,"Sukses");
				tabel_kategori.ajax.reload();
				frame.modal("hide");
			} else {
				toastr.error(respon.pesan,"Gagal");
			}
		})
		.fail(function(){
			toastr.error("Terjadi error di server","Gagal");
		});
	});

	$(document).on("click", ".btn-kategori-delete", function() {
		let id_kategori = $(this).data("id");
		let url = "/kategori/kategori_delete";

		bootbox.confirm("Apakah Anda yakin ingin menghapus kategori ini? Artikel terkait tidak akan dihapus, hanya lepas dari kategori ini.", function (result) {
			if (result) {
				$.post(url, {
					id_kategori: id_kategori
				}, function(res) {
					if (res.status == "sukses") {
						toastr.success(res.pesan);
						tabel_kategori.ajax.reload();
					} else {
						toastr.warning(res.pesan);
					}
				});
			}
		});
	});
});
</script>
<?php $this->load->view('templates/3_end') ?>

```

Contoh halaman `resource/resource_add.php`:

```bash
<form id="form-kategori-add" method="POST">
	<label for="kategori">Nama Kategori</label>
	<input id="kategori" type="text" name="kategori" class="form-control mb-3" required maxlength="20">
	<button type="submit" class="btn btn-primary mt-3 px-4">Save</button>
</form>

```

Contoh halaman `resource/resource_edit.php`:

```bash
<form id="form-kategori-edit" method="POST">
	<input type="hidden" name="id_kategori" value="<?= $kategori->id_kategori ?>">
	<label for="kategori">Nama Kategori</label>
	<input id="kategori" type="text" name="kategori" class="form-control mb-3" required maxlength="20" value="<?= $kategori->kategori ?>">
	<button type="submit" class="btn btn-primary mt-3 px-4">Save</button>
</form>

```

--- 
buatkan logonya dan aku bisa lebih mudah untuk aksesnya dengan PWA
---
bagaimana konsep dan step by step untuk membuat pwa agar web ini bisa dipakai seperti aplikasi biasa? nanti akan aku catat ke notion
---
tambahkan fitur edit progres
---
untuk git nya aku yang mengubahnya, gausah disentuh
---
mengapa perubahan app.css tidak langsung sinkron dengan yang ditampilkan di browser maupun pwa app nya? jika aku hard refresh perubahan itu berhasil diterapkan, sedangakn kalo direfresh biasa berikutnya malah kembali ke versi cached nya. bagaimana agar sinkron?
---
ada penyesuaian lanjutan, jadi aku membutuhkan sebuah fitur detail di setiap tugas progress(di view saat ini aku namai sebagai "Tugas"). untuk setiap tugas memiliki sub tugas. nantinya bisa membuat banyak sub tugas yang mana di dalamnya ada 3 status utamanya lagi yaitu pending, wip, dan selesai(di view aku sebut dengan istilah inbox, progress, dan done)
---
aku sesuaikan lagi, jadi untuk masuk ke detail tugas adalah dengan cara mengganti html utama di halaman board menggunakan ajax, di dalam detail tugas terlihat detail apa saja yang ada di dalam tugas tersebut, nantinya di tugas induk saat di bagian detail ada kolom catatan sehingga user bisa memasukkan catatan di setiap tugasnya(khususnya induk, bukan sub). di bawah detail tugas barulah ada board degnan style yang sama seperti board milik induknya lengkap dengan fitur crud sub tugas serta drag and drop mulai dari status inbox, progress dan done nya
---
```<div class="kcard kcard--sub" data-id="13"><div class="kcard__title kcard__title--sub">meme</div><button type="button" class="kcard__delete btn-subtask-delete" data-id="13" aria-label="Hapus sub tugas">×</button></div>```
elemen ini buat agar tombol x dengan teks sub tugasnya sejajar secara horizontal
---
buatkan aku fitur hover preview untuk card tugas(induk), ketika dihover dalam delay 1 detik. preview yang ditampilkan adalah mirip seperti halaman detailnya mencakup nama, proyek, grup, status, deadline, deskripsi, catatan(maks 20 karakter), dan sub tugas yang ditampilan dalam mini preview max 3 sub tugas lalu tambahkan keterangan berapa sub tugas yang tersembunyi. preview ini akan hilang saat kursor keluar dari card tugasnya. pastikan query tetap ringan agar tidak membuatnya lambat.
---
previewnya mengikuti tepat di bawah kursornya dan menghilang saat kursor keluar dari card. mini kanban boarnya juga tampilkan di dalam previewnya, setiap status max 3 sub tugas yang terlihat di mini preview
---
project ini sudah diupload ke aapanel dan sudah menyambungkan dengan domainnya. hasilnya saat diakses di browser sudah tampil html default sukses milik aapanel, belum menampilkan project web ini. bagaimana pengaturan yang seharusnya untuk project ini?
---
kalo ini error apa?
Warning: realpath(): open_basedir restriction in effect. File(/www/wwwroot/tracker/system) is not within the allowed path(s): (/www/wwwroot/tracker/public/:/tmp/) in /www/wwwroot/tracker/public/index.php on line 199
Warning: is_dir(): open_basedir restriction in effect. File(../system/) is not within the allowed path(s): (/www/wwwroot/tracker/public/:/tmp/) in /www/wwwroot/tracker/public/index.php on line 214
Your system folder path does not appear to be set correctly. Please open the following file and correct this: index.php
---
oke sudah berhasil, di mana nilai dari getenv nya? aku mau edit kredensial database
---
aku sudah edit config databasenya dengan mengisinya hanya dengan string nilai nya saja, karena jika ada getenv nya malah jadi error seperti tadi. sekarang halaman webnya sudash muncul normal di halaman default webnya saat akses domain bakekok.ai.id namun saat pindah ke halaman lain malah error 404, ada apa ini? kan sudah ada htaccess nya dan index.php sudah dihilangkan, hanya bisa diakses jika pake index.php di url nya
---
Subagentsoke sudah beres, sekarang bantu aku untuk catat tata cara ini, nanti akan aku paste ke notion. tulis dalam sesi chat ini sajaShellTerminals