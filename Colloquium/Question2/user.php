<?php

class User {
    private $username;
    private $email;
    private $status;

    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
        $this->status = 'Active'; 
    }

    public function getInfo() {
        return "User: {$this->username}, email: {$this->email}, status - {$this->status}";
    }
}