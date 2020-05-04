<?php
    class response
    {
        public $status;
        public $data;

        public function __construct()
        {
            $this->status = 'unsucces';
            $this->data = array();
        }
    }

?>