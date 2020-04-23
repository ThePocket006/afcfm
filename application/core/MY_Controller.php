<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    class MY_Controller extends CI_Controller
    {
        /**
         *
         * @var array
         */
        protected $data;
        
        /**
         *
         * @var array
         */
        protected $menus;
        
        /**
         * @var string
         */
        protected $zone = '';

        /**
         *
         * @var Moment
         */
        protected $moment;

        /**
         * @var int[0,1]
        */
        protected $section = 1;

        /**
         * @var string
         */
        protected $folder_init = 'bulletin';
        
        function __construct()
        {
            parent::__construct();

            if(session_data('connect')===false And strtolower($this->uri->rsegment(1))!='auth'){
                set_flash_data(['uri_string'=>$this->uri->uri_string()]);
                redirect(site_url('auth'));
            }

            if(isset($_POST['menu_section'])){
                set_session_data(array('section'=>$_POST['menu_section']));
                redirect(site_url(explode('/', explode(site_url(), $_POST['url'])[1])[0]));
            }
        }

        /**
         *
         * @param string $view La vue a charger
         * @param string $titre Le titre de la vue
         * @param bool|string|array $not_menu Les menus à ne pas charger
         */
        protected function render($view = 'index', $titre='', $titre_separator=' - ', $menu=true)
        {
            if(!$view) $view = 'index';
            $this->load->view('header', array('titre'=>array($titre, $titre_separator)));
            if($menu===true){
                $this->load->view('menu', array('section'=>array($this->M_settings->get_SectionID('fr', true), $this->M_settings->get_SectionID('en', true))));
            }
            $this->execute($view);
            $this->load->view('footer', array('menus'=>$this->menus));
        }

        protected function execute($view, $titre='')
        {
            if($titre) $this->data['titre'] = $titre;
            $this->load->view($this->zone.'/'.$view, $this->data);
        }

        protected function logout()
        {
            unset_session_data();
        }

        protected function vardump(...$expression)
        {
            echo "<pre>";
            foreach ($expression as $item) {
                var_dump($item);
            }
            echo "</pre>";
            die();
        }

        protected function ip()
        {
            $result = new stdClass();
            $result->local = $this->local_ip();
            $result->public = $this->public_ip();
            return $result;
        }

        /**
         * @param $file_gs_config string
         * @return bool
         */
        protected function load_config($file_gs_config = '')
        {
            return $this->config->load("gs_config_".$file_gs_config);
        }

        private function public_ip()
        {
            $url = 'https://api.ipify.org';
            $ip = @file_get_contents($url);

            if($this->input->valid_ip($ip)){
                return $ip;
            }
            return NULL;
        }

        private function local_ip()
        {
            $ip = gethostbyname(trim(`hostname`));
            if($this->input->valid_ip($ip)){
                return $ip;
            }
            return NULL;
        }
    }