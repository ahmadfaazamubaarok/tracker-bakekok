<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_progress extends CI_Model {

	public function list_progress($status = '', $archived = '', $id_project = '', $id_group = '')
	{
		if ($status != '') $this->db->where('progresses.status',$status);
		if ($archived != '') $this->db->where('progresses.archived',(int)$archived);
		if ($id_project != '') $this->db->where('progresses.project_id',(int)$id_project);
		if ($id_group != '') $this->db->where('projects.group_id',(int)$id_group);
		$this->db->select('progresses.*, projects.name AS project_name, projects.color AS project_color, project_groups.name AS group_name');
		$this->db->from('progresses');
		$this->db->join('projects','projects.id = progresses.project_id');
		$this->db->join('project_groups','project_groups.id = projects.group_id');
		$this->db->order_by('FIELD(progresses.status,"pending","wip","done")','', FALSE);
		$this->db->order_by('progresses.position','ASC');
		return $this->db->get()->result();
	}

	public function single_progress($id_progress)
	{
		$this->db->select('progresses.*, projects.name AS project_name, projects.color AS project_color, project_groups.name AS group_name');
		$this->db->from('progresses');
		$this->db->join('projects','projects.id = progresses.project_id');
		$this->db->join('project_groups','project_groups.id = projects.group_id');
		$this->db->where('progresses.id',(int)$id_progress);
		return $this->db->get()->row();
	}

	public function add_progress($data)
	{
		return $this->db->insert('progresses',$data);
	}

	public function update_progress($id_progress, $data)
	{
		$this->db->where('id',(int)$id_progress);
		$status = $this->db->update('progresses',$data);
		$this->db->reset_query();
		return $status;
	}

	public function delete_progress($id_progress)
	{
		$this->db->where('id',(int)$id_progress);
		$status = $this->db->delete('progresses');
		$this->db->reset_query();
		return $status;
	}

	// AGREGAT — posisi paling bawah kolom untuk progres baru (BR-03)
	public function max_position($status)
	{
		$this->db->select_max('position');
		$this->db->where('status',$status);
		return $this->db->get('progresses')->row();
	}
}
