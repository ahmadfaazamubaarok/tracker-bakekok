<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_project extends CI_Model {

	public function list_project($id_group = '')
	{
		if ($id_group != '') $this->db->where('projects.group_id',(int)$id_group);
		$this->db->select('projects.*, project_groups.name AS group_name');
		$this->db->from('projects');
		$this->db->join('project_groups','project_groups.id = projects.group_id');
		$this->db->order_by('projects.name','ASC');
		return $this->db->get()->result();
	}

	public function single_project($id_project)
	{
		$this->db->select('projects.*, project_groups.name AS group_name');
		$this->db->from('projects');
		$this->db->join('project_groups','project_groups.id = projects.group_id');
		$this->db->where('projects.id',(int)$id_project);
		return $this->db->get()->row();
	}

	public function add_project($data)
	{
		return $this->db->insert('projects',$data);
	}

	public function update_project($id_project, $data)
	{
		$this->db->where('id',(int)$id_project);
		$status = $this->db->update('projects',$data);
		$this->db->reset_query();
		return $status;
	}

	public function delete_project($id_project)
	{
		$this->db->where('id',(int)$id_project);
		$status = $this->db->delete('projects');
		$this->db->reset_query();
		return $status;
	}

	// PIVOT PROGRESSES — guard hapus proyek (BR-06)
	public function count_progress($id_project)
	{
		$this->db->where('project_id',(int)$id_project);
		return $this->db->count_all_results('progresses');
	}
}
