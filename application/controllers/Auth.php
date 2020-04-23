<?php


class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if(is_connect()){
            redirect();
        }

        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('submit', '', 'trim');
        if($this->form_validation->run())
        {
            $uri = $this->input->post_get('uri');
            set_session_data('connect', true);
            redirect($uri?$uri:'');
        }

        $this->execute('pages/login');
    }
}