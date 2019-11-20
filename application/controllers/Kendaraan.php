<?php

use Restserver\Libraries\REST_Controller;

class Kendaraan extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length,Accept-Encoding");
        parent::__construct();
        $this->load->model('KendaraanModel');
        $this->load->library('form_validation');
    }

    public function index_get()
    {
        $data = $this->verify_request();
        // Send the return data as reponse
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'data' => $data];
        $this->response($response, $status);
        return $this->returnData($this->db->get('vehicles')->result(), false);
    }

    public function index_post($id = null)
    {
        $validation = $this->form_validation;
        $rule = $this->KendaraanModel->rules();
        if ($id == null) {
            array_push(
                $rule,
                [
                    'field' => 'merk',
                    'label' => 'merk',
                    'rules' => 'required'
                ],
                [
                    'field' => 'type',
                    'label' => 'type',
                    'rules' => 'required'
                ],
                [
                    'field' => 'licensePlate',
                    'label' => 'type',
                    'rules' => 'required'
                ],
                [
                    'field' => 'created_at',
                    'label' => 'type',
                    'rules' => ''
                ]
            );
        } else {
            array_push(
                $rule,
                [
                    'field' => 'merk',
                    'label' => 'merk',
                    'rules' => 'required',
                    
                    'field' => 'type',
                    'label' => 'type',
                    'rules' => 'required',
                    
                    'field' => 'licensePlate',
                    'label' => 'type',
                    'rules' => 'required',

                    'field' => 'created_at',
                    'label' => 'type',
                    'rules' => '',
                    
                ]
            );
        }
        $validation->set_rules($rule);
        if (!$validation->run()) {
            return $this->returnData($this->form_validation->error_array(), true);
        }
        $user = new KendaraanData();
        $user->merk = $this->post('merk');
        $user->type = $this->post('type');
        $user->licensePlate = $this->post('licensePlate');
        $user->created_at = $this->post('created_at');
        if ($id == null) {
            $response = $this->KendaraanModel->store($user);
        } else {
            $response = $this->KendaraanModel->update($user, $id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null)
    {
        if ($id == null) {
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->KendaraanModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg, $error)
    {
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }

    private function verify_request()
    {
        // Get all the headers
        $headers = $this->input->request_headers();
        // Extract the token
        $token = $headers['Authorization'];
        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                $this->response($response, $status);
                exit();
            } else {
                return $data;
            }
        } catch (Exception $e) {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            $this->response($response, $status);
        }
    }
}
class KendaraanData
{
    public $merk;
    public $type;
    public $licensePlate;
    public $created_at;
}
