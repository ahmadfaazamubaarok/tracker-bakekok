<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		require_login();
	}

	public function board_filter()
	{
		$data['groups'] = $this->model_group->list_group();
		$data['projects'] = $this->model_project->list_project();
		$this->load->view('board/board_filter',$data);
	}

	public function index()
	{
		$data['navbar'] = 'board';

		$jumlah_group = count($this->model_group->list_group());
		$jumlah_project = count($this->model_project->list_project());

		if ($jumlah_group == 0) {
			$data['mode'] = 'empty_group';
		} else if ($jumlah_project == 0) {
			$data['mode'] = 'empty_project';
		} else {
			$data['mode'] = 'board';
		}

		$this->load->view('board/board_view',$data);
	}
}
