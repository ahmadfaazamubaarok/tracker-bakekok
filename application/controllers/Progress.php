<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Progress extends CI_Controller {

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

	public function progress_daftar()
	{
		// filter board (dari modal filter, persist di localStorage klien)
		$id_project = intval($this->input->get('id_project'));
		$id_group = intval($this->input->get('id_group'));
		$daftar_progress = $this->model_progress->list_progress('', '0', $id_project > 0 ? $id_project : '', $id_group > 0 ? $id_group : '');
		$kolom = array(
			'pending' => array(),
			'wip' => array(),
			'done' => array(),
		);

		// satu query batch untuk sub tugas seluruh kartu yang tampil (bahan hover preview)
		$sub_per_progress = array();
		if (count($daftar_progress) > 0) {
			$ids = array();
			foreach ($daftar_progress as $row) {
				$ids[] = intval($row->id);
			}
			$semua_sub = $this->model_sub_progress->list_sub_progress($ids, '');
			foreach ($semua_sub as $sub) {
				$sub_per_progress[$sub->progress_id][$sub->status][] = $sub;
			}
		}

		foreach ($daftar_progress as $key) {
			$bahan_kartu = array();
			$bahan_kartu['id'] = intval($key->id);
			$bahan_kartu['name'] = html_escape($key->name);
			$bahan_kartu['description'] = $key->description != null ? html_escape($key->description) : '';
			$bahan_kartu['deadline'] = $key->deadline;
			$bahan_kartu['terlambat'] = is_overdue($key->deadline, $key->status);
			$bahan_kartu['priority'] = $key->priority;
			$bahan_kartu['priority_label'] = priority_label($key->priority);
			$bahan_kartu['project_name'] = html_escape($key->project_name);
			$bahan_kartu['project_color'] = $key->project_color;
			$bahan_kartu['group_name'] = html_escape($key->group_name);
			$bahan_kartu['subtask_total'] = intval($this->model_sub_progress->count_sub_progress($key->id, ''));
			$bahan_kartu['subtask_done'] = intval($this->model_sub_progress->count_sub_progress($key->id, 'done'));

			// catatan dipotong maks 20 karakter untuk hover preview
			$notes_preview = '';
			$notes_truncated = FALSE;
			if ($key->notes != null && trim($key->notes) != '') {
				$notes = $key->notes;
				if (function_exists('mb_substr')) {
					$notes_preview = mb_substr($notes, 0, 20, 'UTF-8');
					$notes_truncated = mb_strlen($notes, 'UTF-8') > 20;
				} else {
					$notes_preview = substr($notes, 0, 20);
					$notes_truncated = strlen($notes) > 20;
				}
			}
			$bahan_kartu['notes_preview'] = html_escape($notes_preview);
			$bahan_kartu['notes_truncated'] = $notes_truncated;

			// maks 3 sub tugas per status tampil di mini kanban preview, sisanya dihitung
			$sub_kolom = isset($sub_per_progress[$key->id]) ? $sub_per_progress[$key->id] : array();
			$bahan_kartu['subtasks'] = array(
				'pending' => array(),
				'wip' => array(),
				'done' => array(),
			);
			$bahan_kartu['subtask_hidden'] = array(
				'pending' => 0,
				'wip' => 0,
				'done' => 0,
			);
			foreach (array('pending','wip','done') as $st) {
				$list = isset($sub_kolom[$st]) ? $sub_kolom[$st] : array();
				foreach (array_slice($list, 0, 3) as $sub) {
					$bahan_kartu['subtasks'][$st][] = html_escape($sub->name);
				}
				$bahan_kartu['subtask_hidden'][$st] = max(0, count($list) - 3);
			}

			$kolom[$key->status][] = $bahan_kartu;
		}

		$response = array(
			'status' => 'sukses',
			'data' => $kolom
		);
		$this->set_output($response);
	}

	public function progress_add()
	{
		if (count($this->model_project->list_project()) == 0) {
			redirect('project');
		}

		$data['projects'] = $this->model_project->list_project();
		$data['deadline_default'] = date('Y-m-d', strtotime('+7 days'));

		$this->load->view('progress/progress_add',$data);
	}

	public function progress_addsave()
	{
		$name = $this->input->post('name');
		$id_project = intval($this->input->post('id_project'));
		$deadline = $this->input->post('deadline');
		$description = $this->input->post('description');
		$priority = $this->input->post('priority');

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres maksimal 150 karakter.'
			), 400);
		}

		if ($this->model_project->single_project($id_project) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Proyek tidak valid.'
			), 400);
		}

		if (trim($deadline) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline wajib diisi.'
			), 400);
		}

		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline tidak valid.'
			), 400);
		}

		$pecah = explode('-', $deadline);
		if (!checkdate(intval($pecah[1]), intval($pecah[2]), intval($pecah[0]))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline tidak valid.'
			), 400);
		}

		if (!in_array($priority, array('low','medium','high'))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Prioritas tidak valid.'
			), 400);
		}

		if (strlen(trim($description)) > 1000) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deskripsi maksimal 1000 karakter.'
			), 400);
		}

		// BR-03: progres baru selalu pending, posisi paling bawah kolomnya
		$max = $this->model_progress->max_position('pending');
		$position = $max ? (int) $max->position + 1 : 1;

		$bahan = array(
			'project_id' => $id_project,
			'name' => trim($name),
			'description' => trim($description),
			'deadline' => $deadline,
			'priority' => $priority,
			'status' => 'pending',
			'position' => $position
		);

		if ($this->model_progress->add_progress($bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Progres berhasil ditambahkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menambahkan progres'
		), 500);
	}

	public function progress_edit($id = '')
	{
		$data['progress'] = $this->model_progress->single_progress($id);
		if ($data['progress'] == null) {
			redirect('board');
		}

		$data['projects'] = $this->model_project->list_project();

		$this->load->view('progress/progress_edit',$data);
	}

	public function progress_editsave()
	{
		$id = intval($this->input->post('id'));
		$name = $this->input->post('name');
		$id_project = intval($this->input->post('id_project'));
		$deadline = $this->input->post('deadline');
		$description = $this->input->post('description');
		$priority = $this->input->post('priority');

		if ($this->model_progress->single_progress($id) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		if (trim($name) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres wajib diisi.'
			), 400);
		}

		if (strlen(trim($name)) < 3) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres minimal 3 karakter.'
			), 400);
		}

		if (strlen(trim($name)) > 150) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Nama progres maksimal 150 karakter.'
			), 400);
		}

		if ($this->model_project->single_project($id_project) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Proyek tidak valid.'
			), 400);
		}

		if (trim($deadline) == '') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline wajib diisi.'
			), 400);
		}

		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline tidak valid.'
			), 400);
		}

		$pecah = explode('-', $deadline);
		if (!checkdate(intval($pecah[1]), intval($pecah[2]), intval($pecah[0]))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deadline tidak valid.'
			), 400);
		}

		if (!in_array($priority, array('low','medium','high'))) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Prioritas tidak valid.'
			), 400);
		}

		if (strlen(trim($description)) > 1000) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Deskripsi maksimal 1000 karakter.'
			), 400);
		}

		// Hanya field konten yang boleh diedit — status & position tetap dikelola drag & drop (BR-03..BR-05)
		$bahan = array(
			'project_id' => $id_project,
			'name' => trim($name),
			'description' => trim($description),
			'deadline' => $deadline,
			'priority' => $priority
		);

		if ($this->model_progress->update_progress($id, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Progres berhasil diperbarui.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal mengubah progres'
		), 500);
	}

	public function progress_detail($id = '')
	{
		$data['progress'] = $this->model_progress->single_progress($id);
		if ($data['progress'] == null) {
			redirect('board');
		}

		$this->load->view('progress/progress_detail',$data);
	}

	public function progress_delete()
	{
		$id = intval($this->input->post('id'));

		// sub tugas adalah anak dari progres: ikut terhapus bersama induknya (transaksi)
		$this->db->trans_start();
		$this->model_sub_progress->delete_sub_progress('', $id);
		$this->model_progress->delete_progress($id);
		$this->db->trans_complete();

		if ($this->db->trans_status() == TRUE) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Progres berhasil dihapus.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menghapus progres'
		), 500);
	}

	public function progress_catatan()
	{
		$id = intval($this->input->post('id'));
		$notes = $this->input->post('notes');

		if ($this->model_progress->single_progress($id) === null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		if (strlen(trim($notes)) > 2000) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Catatan maksimal 2000 karakter.'
			), 400);
		}

		if ($this->model_progress->update_progress($id, array('notes' => trim($notes)))) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Catatan berhasil disimpan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal menyimpan catatan'
		), 500);
	}

	public function progress_archive()
	{
		$id = intval($this->input->post('id'));

		$item = $this->model_progress->single_progress($id);
		if ($item == null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		if ($item->status != 'done') {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Hanya progres di kolom Selesai yang bisa diarsipkan.'
			), 400);
		}

		$bahan = array(
			'archived' => 1,
			'archived_at' => date('Y-m-d H:i:s')
		);

		if ($this->model_progress->update_progress($id, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Progres berhasil diarsipkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal mengarsipkan progres'
		), 500);
	}

	public function progress_restore()
	{
		$id = intval($this->input->post('id'));

		$item = $this->model_progress->single_progress($id);
		if ($item == null) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres tidak ditemukan.'
			), 400);
		}

		if (!$item->archived) {
			$this->set_output(array(
				'status' => 'gagal',
				'pesan' => 'Progres ini tidak sedang diarsipkan.'
			), 400);
		}

		$bahan = array(
			'archived' => 0,
			'archived_at' => null
		);

		if ($this->model_progress->update_progress($id, $bahan)) {
			$this->set_output(array(
				'status' => 'sukses',
				'pesan' => 'Progres berhasil dipulihkan.'
			));
		}

		$this->set_output(array(
			'status' => 'gagal',
			'pesan' => 'Gagal memulihkan progres'
		), 500);
	}

	public function progress_reorder()
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

			$item = $this->model_progress->single_progress($id);
			if ($item == null || $item->archived != 0) {
				$this->set_output(array(
					'status' => 'gagal',
					'pesan' => 'Gagal menyimpan posisi. Coba lagi.'
				), 400);
			}
		}

		$this->db->trans_start();
		foreach ($order as $i => $id) {
			$this->model_progress->update_progress($id, array(
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
