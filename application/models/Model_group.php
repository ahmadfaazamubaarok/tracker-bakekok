<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_group extends CI_Model {

	public function list_group()
	{
		$this->db->order_by('name','ASC');
		return $this->db->get('project_groups')->result();
	}

	public function single_group($id_group)
	{
		$this->db->where('id',(int)$id_group);
		return $this->db->get('project_groups')->row();
	}

	public function add_group($data)
	{
		return $this->db->insert('project_groups',$data);
	}

	public function update_group($id_group, $data)
	{
		$this->db->where('id',(int)$id_group);
		$status = $this->db->update('project_groups',$data);
		$this->db->reset_query();
		return $status;
	}

	public function delete_group($id_group)
	{
		$this->db->where('id',(int)$id_group);
		$status = $this->db->delete('project_groups');
		$this->db->reset_query();
		return $status;
	}

	// PIVOT PROJECTS — guard hapus grup (BR-06)
	public function count_project($id_group)
	{
		$this->db->where('group_id',(int)$id_group);
		return $this->db->count_all_results('projects');
	}
}
