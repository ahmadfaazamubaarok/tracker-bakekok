<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subtask extends CI_Controller {

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

	// Daftar sub tugas per progres, terkelompok per status (Inbox/Progress/Done di view)
	public function subtask_daftar($id_progress = '')
	{
		$progress = $this->model_progress->single_progress($id_progress);
		if ($progress == null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		$daftar = $this->model_sub_progress->list_sub_progress($id_progress, '');
		$kolom = array(
			'pending' => array(),
			'wip' => array(),
			'done' => array(),
		);

		foreach ($daftar as $key) {
			$bahan = array();
			$bahan['id'] = intval($key->id);
			$bahan['name'] = html_escape($key->name);
			$bahan['status'] = $key->status;

			$kolom[$key->status][] = $bahan;
		}

		$response = array(
			'status' => 'sukses',
			'data' => $kolom
		);
		$this->set_output($response);
	}

	public function subtask_addsave()
	{
		$id_progress = intval($this->input->post('id_progress'));
		$name = $this->input->post('name');

		if ($this->model_progress->single_progress($id_progress) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama sub tugas wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama sub tugas minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama sub tugas maksimal 150 karakter.'
			), 400);
		}

		// sub tugas baru selalu masuk grup Inbox (pending), posisi paling bawah
		$max = $this->model_sub_progress->max_position($id_progress);
		$position = $max ? (int) $max->position + 1 : 1;

		$bahan = array(
			'progress_id' => $id_progress,
			'name' => trim($name),
			'status' => 'pending',
			'position' => $position
		);

		if ($this->model_sub_progress->add_sub_progress($bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Sub tugas berhasil ditambahkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menambahkan sub tugas'
		), 500);
	}

	// Ubah status sub tugas (pending -> wip -> done, diklik dari chip)
	public function subtask_status()
	{
		$id = intval($this->input->post('id'));
		$status = $this->input->post('status');

		if ($this->model_sub_progress->single_sub_progress($id) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Sub tugas tidak ditemukan.'
			), 400);
		}

		if (!in_array($status, array('pending','wip','done'))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Status sub tugas tidak valid.'
			), 400);
		}

		if ($this->model_sub_progress->update_sub_progress($id, array('status' => $status))) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => ''
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal mengubah status sub tugas'
		), 500);
	}

	public function subtask_delete()
	{
		$id = intval($this->input->post('id'));

		if ($this->model_sub_progress->delete_sub_progress($id, '')) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Sub tugas berhasil dihapus.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menghapus sub tugas'
		), 500);
	}

	// Drag & drop antar grup status (Inbox/Progress/Done) — kontrak sama dengan progress_reorder
	public function subtask_reorder()
	{
		$status = $this->input->post('status');
		$order = $this->input->post('order');

		if (!in_array($status, array('pending','wip','done'))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
			), 400);
		}

		if (!is_array($order)) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
			), 400);
		}

		foreach ($order as $id) {
			if (!is_numeric($id)) {
				$this->set_output(array(
					'status' => 'gagal',
					'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
				), 400);
			}

			$item = $this->model_sub_progress->single_sub_progress($id);
			if ($item == null) {
				$this->set_output(array(
					'status' => 'gagal',
					'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
				), 400);
			}
		}

		$this->db->trans_start();
		foreach ($order as $i => $id) {
			$this->model_sub_progress->update_sub_progress($id, array(
				'status' => $status,
				'position' => intval($i)
			));
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() == TRUE) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => ''
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
		), 500);
	}
}
