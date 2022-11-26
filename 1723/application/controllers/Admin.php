<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function index()
	{
        // $this->load->view('welcome_message');
        echo "hello";
    }
    
    public function getAdmin()
    {
        $this->load->model('M_admin');
        $data=$this->M_admin->getData();

        $result=array(
            "success" => true,
            "message"=>"Data found",
            "data"=>$data
        );

        echo json_encode($result);
    }

    public function addData()
    {
        $data=array(
            'email'=>$this->post('email'),
            'password'=>$this->post('password'),
            'nama'=>$this->post('nama')
        );
        $insert=$this->M_admin->insertData($data);

        if($insert){
            $this->response($data,200);
        }else{
            $this->response($data,502);
        }
    }
}
