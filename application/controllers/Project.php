<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

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
		$data['navbar'] = 'project';

		if (count($this->model_group->list_group()) == 0) {
			$data['mode'] = 'empty_group';
		} else {
			$data['mode'] = 'list';
		}

		$this->load->view('project/project_view',$data);
	}

	public function project_daftar()
	{
		$daftar_project = $this->model_project->list_project();
		$daftar_input = array();

		foreach ($daftar_project as $key) {

			$nama_html = '<span class="dot-label"><span class="dot" style="background-color:'.$key->color.'"></span>'.html_escape($key->name).'</span>';

			$domain_href = preg_match('#^https?://#i', $key->domain) ? $key->domain : 'https://'.$key->domain;
			$domain_html = '<a class="link-domain" href="'.html_escape($domain_href).'" target="_blank" rel="noopener">'.html_escape($key->domain).'</a>';

			$direktori_html = '<code>'.html_escape($key->directory_path).'</code>';

			$aksi_html = '
				<div class="dropdown">
					<button class="btn btn-outline-secondary btn-xs btn-aksi dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item btn-project-edit" data-id="'.$key->id.'" href="javascript:;">Edit</a>
						<a class="dropdown-item btn-project-delete" data-id="'.$key->id.'" href="javascript:;">Delete</a>
					</div>
				</div>';

			$bahan_input = array(
				$nama_html,
				html_escape($key->group_name),
				$domain_html,
				$direktori_html,
				$this->model_project->count_progress($key->id),
				$aksi_html,
			);

			array_push($daftar_input, $bahan_input);
		}

		$response = array(
			'data' => $daftar_input
		);
		$this->set_output($response);
	}

	public function project_add()
	{
		if (count($this->model_group->list_group()) == 0) {
			redirect('group');
		}

		$data['groups'] = $this->model_group->list_group();

		$palet = array('#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899');
		$no = count($this->model_project->list_project());
		$data['color'] = $palet[$no % count($palet)];

		$this->load->view('project/project_add',$data);
	}

	public function project_addsave()
	{
		$name = $this->input->post('name');
		$id_group = intval($this->input->post('id_group'));
		$description = $this->input->post('description');
		$domain = $this->input->post('domain');
		$directory_path = $this->input->post('directory_path');
		$color = $this->input->post('color');

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek maksimal 150 karakter.'
			), 400);
		}

		if ($this->model_group->single_group($id_group) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Grup tidak valid.'
			), 400);
		}

		if (trim($domain) == '' || strlen(trim($domain)) > 255) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Domain wajib diisi.'
			), 400);
		}

		if (trim($directory_path) == '' || strlen(trim($directory_path)) > 255) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Direktori wajib diisi.'
			), 400);
		}

		if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Warna tidak valid.'
			), 400);
		}

		if (strlen(trim($description)) > 1000) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deskripsi maksimal 1000 karakter.'
			), 400);
		}

		$bahan = array(
			'group_id' => $id_group,
			'name' => trim($name),
			'description' => trim($description),
			'domain' => trim($domain),
			'directory_path' => trim($directory_path),
			'color' => $color
		);

		if ($this->model_project->add_project($bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Proyek berhasil ditambahkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menambahkan proyek'
		), 500);
	}

	public function project_edit($id = '')
	{
		$data['project'] = $this->model_project->single_project($id);
		if ($data['project'] == null) {
			redirect('project');
		}

		$data['groups'] = $this->model_group->list_group();

		$this->load->view('project/project_edit',$data);
	}

	public function project_editsave()
	{
		$id = intval($this->input->post('id'));
		$name = $this->input->post('name');
		$id_group = intval($this->input->post('id_group'));
		$description = $this->input->post('description');
		$domain = $this->input->post('domain');
		$directory_path = $this->input->post('directory_path');
		$color = $this->input->post('color');

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama proyek maksimal 150 karakter.'
			), 400);
		}

		if ($this->model_group->single_group($id_group) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Grup tidak valid.'
			), 400);
		}

		if (trim($domain) == '' || strlen(trim($domain)) > 255) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Domain wajib diisi.'
			), 400);
		}

		if (trim($directory_path) == '' || strlen(trim($directory_path)) > 255) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Direktori wajib diisi.'
			), 400);
		}

		if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Warna tidak valid.'
			), 400);
		}

		if (strlen(trim($description)) > 1000) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deskripsi maksimal 1000 karakter.'
			), 400);
		}

		$bahan = array(
			'group_id' => $id_group,
			'name' => trim($name),
			'description' => trim($description),
			'domain' => trim($domain),
			'directory_path' => trim($directory_path),
			'color' => $color
		);

		if ($this->model_project->update_project($id, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Proyek berhasil diperbarui.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal mengubah proyek'
		), 500);
	}

	public function project_delete()
	{
		$id = intval($this->input->post('id'));

		if ($this->model_project->count_progress($id) > 0) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Proyek tidak bisa dihapus karena masih memiliki progres.'
			), 400);
		}

		if ($this->model_project->delete_project($id)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Proyek berhasil dihapus.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menghapus proyek'
		), 500);
	}
}
