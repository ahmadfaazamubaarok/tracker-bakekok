<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive extends CI_Controller {

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
		$data['navbar'] = 'archive';
		$this->load->view('archive/archive_view',$data);
	}

	public function archive_daftar()
	{
		$daftar_progress = $this->model_progress->list_progress('', '1', '', '');
		$daftar_input = array();

		foreach ($daftar_progress as $key) {

			$nama_proyek_html = '<span class="dot-label"><span class="dot" style="background-color:'.$key->project_color.'"></span>'.html_escape($key->project_name).'</span>';

			$prioritas_html = '<span class="chip chip--'.$key->priority.'">'.priority_label($key->priority).'</span>';

			$aksi_html = '
				<div class="d-flex align-items-center" style="gap:6px">
					<button type="button" class="btn btn-soft btn-xs btn-archive-restore" data-id="'.$key->id.'">Pulihkan</button>
					<button type="button" class="btn btn-outline-secondary btn-xs btn-progress-delete" data-id="'.$key->id.'" aria-label="Hapus permanen">&times;</button>
				</div>';

			$bahan_input = array(
				html_escape($key->name),
				$nama_proyek_html,
				html_escape($key->group_name),
				$prioritas_html,
				format_tanggal($key->deadline),
				format_tanggal($key->archived_at),
				$aksi_html,
			);

			array_push($daftar_input, $bahan_input);
		}

		$response = array(
			'data' => $daftar_input
		);
		$this->set_output($response);
	}
}
