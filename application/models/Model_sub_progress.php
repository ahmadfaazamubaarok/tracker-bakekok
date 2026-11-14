<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_sub_progress extends CI_Model {

	public function list_sub_progress($id_progress, $status = '')
	{
		if ($status != '') $this->db->where('status',$status);
		if (is_array($id_progress)) {
			// batch: satu query untuk banyak progres (mis. hover preview board)
			$this->db->where_in('progress_id', array_map('intval', $id_progress));
		} else {
			$this->db->where('progress_id',(int)$id_progress);
		}
		$this->db->order_by('position','ASC');
		return $this->db->get('sub_progresses')->result();
	}

	public function single_sub_progress($id_sub_progress)
	{
		$this->db->where('id',(int)$id_sub_progress);
		return $this->db->get('sub_progresses')->row();
	}

	public function add_sub_progress($data)
	{
		return $this->db->insert('sub_progresses',$data);
	}

	public function update_sub_progress($id_sub_progress, $data)
	{
		$this->db->where('id',(int)$id_sub_progress);
		$status = $this->db->update('sub_progresses',$data);
		$this->db->reset_query();
		return $status;
	}

	public function delete_sub_progress($id_sub_progress = '', $id_progress = '')
	{
		if ($id_sub_progress == '' && $id_progress == '') {
			return FALSE;
		}

		if ($id_sub_progress != '') $this->db->where('id',(int)$id_sub_progress);
		if ($id_progress != '') $this->db->where('progress_id',(int)$id_progress);
		$status = $this->db->delete('sub_progresses');
		$this->db->reset_query();
		return $status;
	}

	// AGREGAT — jumlah sub tugas (opsional filter status, mis. selesai)
	public function count_sub_progress($id_progress, $status = '')
	{
		if ($status != '') $this->db->where('status',$status);
		$this->db->where('progress_id',(int)$id_progress);
		return $this->db->count_all_results('sub_progresses');
	}

	// AGREGAT — posisi paling bawah untuk sub tugas baru
	public function max_position($id_progress)
	{
		$this->db->select_max('position');
		$this->db->where('progress_id',(int)$id_progress);
		return $this->db->get('sub_progresses')->row();
	}
}
