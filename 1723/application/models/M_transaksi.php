<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_transaksi extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function gettransaksi()
    {
        $this->db->select('transaksi.id,transaksi.total,transaksi.tanggal,admin.nama');
        $this->db->from('transaksi');
        $this->db->join('admin', 'admin.id = transaksi.admin_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function gettransaksibulanini()
    {
        $this->db->select('transaksi.id,transaksi.total,transaksi.tanggal,admin.nama');
        $this->db->from('transaksi');
        $this->db->join('admin', 'admin.id = transaksi.admin_id');
        $this->db->where('month(tanggal)', date('m'));
        $query = $this->db->get();
        return $query->result_array();
    }


    public function inserttransaksi($data)
    {
        $this->db->insert('transaksi', $data);

        $insert_id  = $this->db->insert_id();
        $result  = $this->db->get_where('transaksi', array('id' => $insert_id));

        return $result->row_array();
    }


    public function updatetransaksi($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('transaksi', $data);

        $result = $this->db->get_where('transaksi', array('id' => $id));

        return $result->row_array();
    }

    public function deletetransaksi($id)
    {
        $result = $this->db->get_where('transaksi', array('id' => $id));

        $this->db->where('id', $id);
        $this->db->delete('transaksi');

        return $result->row_array();
    }

    public function cektransaksiExist($id)
    {
        $data = array(
            "id" => $id
        );

        $this->db->where($data);
        $result = $this->db->get('transaksi');

        if (empty($result->row_array())) {
            return false;
        }

        return true;
    }

    public function cekLogintransaksi($data)
    {
        $this->db->where($data);
        $result = $this->db->get('transaksi');

        return $result->row_array();
    }
}
