<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ajax_Artikel extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Ajax_Artikel_Model','artikel');
	}
	public function index()
	{
		$this->load->helper('url');
		$this->load->view('Ajax_Artikel_View');
	}
	public function ajax_list()
	{
		$list = $this->artikel->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $artikel) {
			$no++;
			$row = array();
			$row[] = $artikel->judul;
			$row[] = $artikel->isi_konten;
			$row[] = $artikel->email;
			$row[] = $artikel->tanggal_buat;
			$row[] = $artikel->nama;

			//add html for action
			$row[] =
			'<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_artikel('."'".$artikel->id_artikel."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
		     <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_artikel('."'".$artikel->id_artikel."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		     
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->artikel->count_all(),
						"recordsFiltered" => $this->artikel->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	public function ajax_edit($id_artikel)
	{
		$data = $this->artikel->get_by_id($id_artikel);
		$data->tanggal_buat = ($data->tanggal_buat == '0000-00-00') ? '' : $data->tanggal_buat; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$tanggal = date("Y-m-d");
		$data = array(
				'judul' => $this->input->post('judul'),
				'isi_konten' => $this->input->post('isi_konten'),
				'email' => $this->input->post('email'),
				'tanggal_buat' => $tanggal,
				'nama' => $this->input->post('nama'),
			);
		$insert = $this->artikel->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$tanggal = date("Y-m-d");
		$data = array(
				'judul' => $this->input->post('judul'),
				'isi_konten' => $this->input->post('isi_konten'),
				'email' => $this->input->post('email'),
				'tanggal_buat' => $tanggal,
				'nama' => $this->input->post('nama'),
			);
		$this->artikel->update(array('id_artikel' => $this->input->post('id_artikel')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_artikel)
	{
		$this->artikel->delete_by_id($id_artikel);
		echo json_encode(array("status" => TRUE));
	}

    function ajax_read(){
		$id_artikel = $this->uri->segment(3);
		$data['artikel']=$this->Ajax_Artikel_Model->select_one($id_artikel)->row_array();
		$this->load->view('detail2',$data);
	}
	
	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('judul') == '')
		{
			$data['inputerror'][] = 'judul';
			$data['error_string'][] = 'Judul is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('isi_konten') == '')
		{
			$data['inputerror'][] = 'isi_konten';
			$data['error_string'][] = 'Konten is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('email') == '')
		{
			$data['inputerror'][] = 'email';
			$data['error_string'][] = 'Email is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Name is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}