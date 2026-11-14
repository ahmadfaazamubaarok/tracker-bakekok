<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		require_login();
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
		$data['navbar'] = 'group';
		$this->load->view('group/group_view',$data);
	}

	public function group_daftar()
	{
		$daftar_group = $this->model_group->list_group();
		$daftar_input = array();

		foreach ($daftar_group as $key) {

			$aksi_html = '
				<div class="dropdown">
					<button class="btn btn-outline-secondary btn-xs btn-aksi dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item btn-group-edit" data-id="'.$key->id.'" href="javascript:;">Edit</a>
						<a class="dropdown-item btn-group-delete" data-id="'.$key->id.'" href="javascript:;">Delete</a>
					</div>
				</div>';

			$bahan_input = array(
				html_escape($key->name),
				$this->model_group->count_project($key->id),
				$aksi_html,
			);

			array_push($daftar_input, $bahan_input);
		}

		$response = array(
			'data' => $daftar_input
		);
		$this->set_output($response);
	}

	public function group_add()
	{
		$this->load->view('group/group_add');
	}

	public function group_addsave()
	{
		$name = $this->input->post('name');

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup maksimal 150 karakter.'
			), 400);
		}

		$daftar_group = $this->model_group->list_group();
		foreach ($daftar_group as $row) {
			if (strtolower($row->name) == strtolower(trim($name))) {
				$this->set_output(array(
					'status' => 'gagal',
					'pesan' => 'Nama grup sudah dipakai.'
				), 400);
			}
		}

		$bahan = array(
			'name' => trim($name)
		);

		if ($this->model_group->add_group($bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Grup berhasil ditambahkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menambahkan grup'
		), 500);
	}

	public function group_edit($id = '')
	{
		$data['group'] = $this->model_group->single_group($id);
		if ($data['group'] == null) {
			redirect('group');
		}
		$this->load->view('group/group_edit',$data);
	}

	public function group_editsave()
	{
		$id = intval($this->input->post('id'));
		$name = $this->input->post('name');

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama grup maksimal 150 karakter.'
			), 400);
		}

		$daftar_group = $this->model_group->list_group();
		foreach ($daftar_group as $row) {
			if ($row->id != $id && strtolower($row->name) == strtolower(trim($name))) {
				$this->set_output(array(
					'status' => 'gagal',
					'pesan' => 'Nama grup sudah dipakai.'
				), 400);
			}
		}

		$bahan = array(
			'name' => trim($name)
		);

		if ($this->model_group->update_group($id, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Grup berhasil diperbarui.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal mengubah grup'
		), 500);
	}

	public function group_delete()
	{
		$id = intval($this->input->post('id'));

		if ($this->model_group->count_project($id) > 0) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Grup tidak bisa dihapus karena masih memiliki proyek.'
			), 400);
		}

		if ($this->model_group->delete_group($id)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Grup berhasil dihapus.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menghapus grup'
		), 500);
	}
}
