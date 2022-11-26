<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/Firebase/JWT/JWT.php';

use \Firebase\JWT\JWT;

class Api_pcs extends REST_Controller
{
    private $secret_key = "amikom";

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_admin');
        $this->load->model('M_produk');
        $this->load->model('M_transaksi');
        $this->load->model('M_item_transaksi');
    }
    //========== cek Token ==============//
    public function cekToken()
    {
        try {
            $token = $this->input->get_request_header('Authorization');

            if (!empty($token)) {
                $token = explode(' ', $token)[1];
            }

            $token_decode = JWT::decode($token, $this->secret_key, array('HS256'));
        } catch (Exception $e) {
            $data_json = array(
                "success" => false,
                "message" => "Token tidak valid",
                "error_code" => 1204,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }
    //========== END cek Token ==============//


    //========= API ADMIN // API ADMIN  ========//
    public function admin_get()
    {   // panggil data
        // cek token
        $this->cekToken();

        // panggil data admin
        $data = $this->M_admin->getData();

        // menampilkan data yang telah di panggil 
        $result = array(
            "success" => true,
            "message" => "Data found",
            "data" => $data
        );

        echo json_encode($result);
    }

    public function admin_post()
    {   // upload data
        // cek token
        // $this->cekToken();
        // menagkap data 
        $data = array(
            'email' => $this->post('email'),
            'password' => md5($this->post('password')),
            'nama' => $this->post('nama')
        );
        // proses input data 
        $insert = $this->M_admin->insertData($data);

        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response($data, 502);
        }
    }

    public function admin_put()
    {   //rubah data
        // cek token
        $this->cekToken();
        // validasi
        $validation_message = [];

        if ($this->put("email") == "") {
            array_push($validation_message, "Email tidak boleh kosong");
        }

        if ($this->put("email") != "" && !filter_var($this->put("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email tidak valid");
        }

        if ($this->put("password") == "") {
            array_push($validation_message, "Password tidak boleh kosong");
        }

        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }
        // if no valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        // if valid
        // tangkap data 
        $data = array(
            "email" => $this->put("email"),
            "password" => md5($this->put("password")),
            "nama" => $this->put("nama")
        );

        $id = $this->put("id");
        // proses rubah data 
        $result = $this->M_admin->updateAdmin($data, $id);
        // if success
        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "admin" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function admin_delete()
    {   // hapus data
        // cekToken
        $this->cekToken();
        // tangkap id yang di terima
        $id = $this->delete("id");
        // panggil function hapus data dengan kirin parameter id
        $result = $this->M_admin->deleteAdmin($id);

        // jika tidak berhasil 
        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        // jika proses berhasil
        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "admin" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    //============ END API ADMIN// END API ADMIN ===========//



    //============= API LOGIN  // API LOGIN ===============//

    public function login_post()
    {   // proses login
        // tangkap data 
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password"))
        );
        // panggil function cekLoginAdmin, 
        $result = $this->M_admin->cekLoginAdmin($data);

        if (empty($result)) {
            // jika no valid
            $data_json = array(
                "success" => false,
                "message" => "Email dan Password tidak valid",
                "error_code" => 1308,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        } else {
            // jika valid, create token
            $date = new Datetime();

            $payload["id"] = $result["id"];
            $payload["email"] = $result["email"];
            $payload["iat"] = $date->getTimestamp();
            $payload["exp"] = $date->getTimestamp() + 3600;

            $data_json = array(
                "success" => true,
                "message" => "Otentikasi Berhasil",
                "data" => array(
                    "admin" => $result,
                    "token" => JWT::encode($payload, $this->secret_key)
                )
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        }
    }

    //=========== END API LOGIN// END API LOGIN =============//



    //=============== API produk  // API produk ============//
    public function produk_get()
    {
        $this->cekToken();
        // panggil data produk 
        $result = $this->M_produk->getProduk();
        // tampilkan data 
        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "produk" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function produk_post()
    {   // upload produk
        // cek token
        $this->cekToken();
        // validasi
        $validation_message = [];

        if ($this->post("admin_id") == "") {
            array_push($validation_message, "Admin ID tidak boleh kosong");
        }
        if ($this->post("admin_id") == "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
            array_push($validation_message, "Admin ID tidak ditemukan");
        }
        if ($this->post("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }
        if ($this->post("harga") == "") {
            array_push($validation_message, "Harga tidak boleh kosong");
        }
        if ($this->post("harga") == "" && !is_numeric($this->input->post("harga"))) {
            array_push($validation_message, "Harga harus di isi angka");
        }
        if ($this->post("stok") == "") {
            array_push($validation_message, "Stok tidak boleh kosong");
        }
        if ($this->post("stok") == "" && !is_numeric($this->input->post("stok"))) {
            array_push($validation_message, "Stok harus di isi angka");
        }
        // if no valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //if lolos validasi
        //tangkap data 
        $data = array(
            'admin_id' => $this->input->post('admin_id'),
            'nama' => $this->input->post('nama'),
            'harga' => $this->input->post('harga'),
            'stok' => $this->input->post('stok')
        );
        // proses insert data
        $result = $this->M_produk->insertProduk($data);
        // tampilkan data 
        $data_json = array(
            "success" => true,
            "message" => "insert Berhasil",
            "data" => array(
                "produk" => $result
            )
        );
        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function produk_put()
    {   //edit produk
        //cek token
        $this->cekToken();
        //validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "ID tidak boleh kosong");
        }
        if ($this->put("admin_id") == "") {
            array_push($validation_message, "Admin ID tidak boleh kosong");
        }
        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama tidak boleh kosong");
        }
        if ($this->put("harga") == "") {
            array_push($validation_message, "Harga tidak boleh kosong");
        }
        if ($this->put("harga") == "" && !is_numeric($this->put("harga"))) {
            array_push($validation_message, "Harga harus di isi angka");
        }
        if ($this->put("stok") == "") {
            array_push($validation_message, "Stok tidak boleh kosong");
        }
        if ($this->put("stok") == "" && !is_numeric($this->put("stok"))) {
            array_push($validation_message, "stok harus di isi angka");
        }
        //if not valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //if validasi, tangkap data 
        $data = array(
            'admin_id' => $this->put('admin_id'),
            'nama' => $this->put('nama'),
            'harga' => $this->put('harga'),
            'stok' => $this->put('stok')
        );

        $id = $this->put("id");
        //proses ubah data 
        $result = $this->M_produk->updateProduk($data, $id);
        //if success, tampilkan  
        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "produk" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function produk_delete()
    {   //delete
        //cek token 
        $this->cekToken();
        //tangkap id
        $id = $this->delete("id");
        // function proses delete
        $result = $this->M_produk->deleteProduk($id);
        // if id not valid
        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //if proses success
        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "produk" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //========= API END produk  // API END produk =============//



    //============ API transaksi // API transaksi =============//
    public function transaksi_get()
    {   //panggil data
        //cek token
        $this->cekToken();
        //function proses panggil data
        $data = $this->M_transaksi->gettransaksi();
        //if data ada, tampilkan 
        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => $data
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_bulan_ini_get()
    {   //panggil data selama satu bulan terakhir 
        //cek token
        $this->cekToken();
        //function proses panggil data
        $data = $this->M_transaksi->gettransaksibulanini();
        //tampil data 
        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => $data
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_post()
    {   //up data
        //cek token
        $this->cekToken();
        //validasi
        $validation_message = [];

        if ($this->input->post("admin_id") == "") {
            array_push($validation_message, "Admin ID tidak boleh kosong");
        }
        if ($this->input->post("admin_id") == "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
            array_push($validation_message, "Admin ID tidak ditemukan");
        }
        if ($this->input->post("total") == "") {
            array_push($validation_message, "total tidak boleh kosong");
        }
        if ($this->input->post("total") == "" && !is_numeric($this->input->post("total"))) {
            array_push($validation_message, "total harus di isi angka");
        }
        //show if not valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //tangkap data 
        $data = array(
            'admin_id' => $this->input->post('admin_id'),
            'total' => $this->input->post('total'),
            'tanggal' => date("Y-m-d H:i:s")
        );
        //proses insert data transaksi
        $result = $this->M_transaksi->inserttransaksi($data);

        //show if data valid
        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_put()
    {   //edit transaksi
        //cektoken
        $this->cekToken();
        //validasi
        $validation_message = [];
        if ($this->put("id") == "") {
            array_push($validation_message, "ID tidak boleh kosong");
        }
        if ($this->put("admin_id") == "") {
            array_push($validation_message, "Admin ID tidak boleh kosong");
        }
        if ($this->put("admin_id") == "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "Admin ID tidak ditemukan");
        }
        if ($this->put("total") == "") {
            array_push($validation_message, "total tidak boleh kosong");
        }
        if ($this->put("total") == "" && !is_numeric($this->put("total"))) {
            array_push($validation_message, "total harus di isi angka");
        }
        //show if data not valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //jika lolos validasi
        //tangkap data
        $data = array(
            'admin_id' => $this->put("admin_id"),
            'total' => $this->put("total"),
            'tanggal' => date("Y-m-d H:i:s")
        );

        //function proses update data
        $id = $this->put("id");
        $result = $this->M_transaksi->updatetransaksi($data, $id);
        //show data if valid
        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_delete()
    {   //delete transaksi
        //cekToken
        $this->cekToken();
        //tangkap data id
        $id = $this->delete("id");
        //function proses delete
        $result = $this->M_transaksi->deletetransaksi($id);
        //show if not valid
        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //show if valid
        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    //======== API END transaksi // API END transaksi ==========//



    //======== API item_transaksi // API item_transaksi =========//
    public function item_transaksi_get()
    {   //show data
        //cek token
        $this->cekToken();
        //function ambil data 
        $result = $this->M_item_transaksi->getitemtransaksi();
        //show if found
        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_get()
    {   //show data
        //cek token
        $this->cekToken();
        //function ambil data whare tansaksi id
        $result = $this->M_item_transaksi->getitemtransaksibytransaksiID($this->input->get('transaksi_id'));
        //show if found
        $data_json = array(
            "success" => true,
            "message" => "Data found",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_post()
    {   //up data 
        //cek token
        $this->cekToken();
        //validation
        $validation_message = [];

        if ($this->input->post("transaksi_id") == "") {
            array_push($validation_message, "transaksi_id tidak boleh kosong");
        }
        if ($this->input->post("transaksi_id") == "" && !$this->M_transaksi->cektransaksiExist($this->input->post("transaksi_id"))) {
            array_push($validation_message, "transaksi_id tidak ditemukan");
        }
        if ($this->input->post("produk_id") == "") {
            array_push($validation_message, "produk_id tidak boleh kosong");
        }
        if ($this->input->post("produk_id") == "" && !$this->M_produk->cekprodukExist($this->input->post("produk_id"))) {
            array_push($validation_message, "produk_id tidak ditemukan");
        }
        if ($this->input->post("qty") == "") {
            array_push($validation_message, "qty tidak boleh kosong");
        }
        if ($this->input->post("qty") == "" && !is_numeric($this->input->post("qty"))) {
            array_push($validation_message, "qty harus di isi angka");
        }
        if ($this->input->post("harga_saat_transaksi") == "") {
            array_push($validation_message, "harga_saat_transaksi tidak boleh kosong");
        }
        if ($this->input->post("harga_saat_transaksi") == "" && !is_numeric($this->input->post("harga_saat_transaksi"))) {
            array_push($validation_message, "harga_saat_transaksi harus di isi angka");
        }
        //show if not valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //jika lolos validasi
        //tangkap data
        $data = array(
            'transaksi_id' => $this->input->post('transaksi_id'),
            'produk_id' => $this->input->post('produk_id'),
            'qty' => $this->input->post('qty'),
            'harga_saat_transaksi' => $this->input->post('harga_saat_transaksi'),
            'sub_total' => $this->input->post('qty') * $this->input->post('harga_saat_transaksi')
        );
        //function proses input data
        $result = $this->M_item_transaksi->insertitemtransaksi($data);

        //show if success
        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_put()
    {   //edit data
        //cektoken
        $this->cekToken();
        //validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "id tidak boleh kosong");
        }
        if ($this->put("transaksi_id") == "") {
            array_push($validation_message, "transaksi_id tidak boleh kosong");
        }
        if ($this->put("transaksi_id") == "" && !$this->M_transaksi->cektransaksiExist($this->put("transaksi_id"))) {
            array_push($validation_message, "transaksi_id tidak ditemukan");
        }
        if ($this->put("produk_id") == "") {
            array_push($validation_message, "produk_id tidak boleh kosong");
        }
        if ($this->put("produk_id") == "" && !$this->M_produk->cekprodukExist($this->put("produk_id"))) {
            array_push($validation_message, "produk_id tidak ditemukan");
        }
        if ($this->put("qty") == "") {
            array_push($validation_message, "qty tidak boleh kosong");
        }
        if ($this->put("qty") == "" && !is_numeric($this->put("qty"))) {
            array_push($validation_message, "qty harus di isi angka");
        }
        if ($this->put("harga_saat_transaksi") == "") {
            array_push($validation_message, "harga_saat_transaksi tidak boleh kosong");
        }
        if ($this->put("harga_saat_transaksi") == "" && !is_numeric($this->put("harga_saat_transaksi"))) {
            array_push($validation_message, "harga_saat_transaksi harus di isi angka");
        }
        //show if not valid
        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data tidak valid",
                "data" => $validation_message
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //jika lolos validasi
        //tangkap data
        $data = array(
            'transaksi_id' => $this->put('transaksi_id'),
            'produk_id' => $this->put('produk_id'),
            'qty' => $this->put('qty'),
            'harga_saat_transaksi' => $this->put('harga_saat_transaksi'),
            'sub_total' => $this->put('qty') * $this->put('harga_saat_transaksi')
        );

        //function proses update data
        $id = $this->put("id");
        $result = $this->M_item_transaksi->updateitem_transaksi($data, $id);
        //show if success
        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_delete()
    { //edit data
        //cektoken
        $this->cekToken();
        //tangkap data id
        $id = $this->delete("id");
        //function proses delete
        $result = $this->M_item_transaksi->deleteitem_transaksi($id);
        //if id not valid
        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //show, if success
        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_delete()
    {   //delete data
        //cektoken
        $this->cekToken();
        //tangkap data
        $transaksi_id = $this->delete("transaksi_id");
        //function delete data where transaksi_id
        $result = $this->M_item_transaksi->deleteitem_transaksibytransaksiID($transaksi_id);
        //show if not valid
        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Id tidak valid",
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //show if success
        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    //===== API end item_transaksi // API end item_transaksi =======//
}
