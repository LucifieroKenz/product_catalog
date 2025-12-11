<?php
class User {
    private $file = "userdata.json";
    private $users = [];

    public function __construct() {
        if (file_exists($this->file)) {
            $data = json_decode(file_get_contents($this->file), true);
            $this->users = $data?$data:[];
        }
        else {
            $this->users = [];
        }
    }

    public function existingUsername($username) {
        foreach ($this->users as $user) {
            if ($user["username"] === $username) {
                return true;
            }
        }
        return false;
    }

    public function register($username, $password) {
        $this->users[]=[
            "username"=>$username,
            "password"=>password_hash($password, PASSWORD_DEFAULT),
        ];
        file_put_contents($this->file, json_encode($this->users, JSON_PRETTY_PRINT));
        return "Registered Successfully!";
    }

    public function getUsername($username) {
        foreach ($this->users as $user) {
            if ($user["username"] === $username) {
                return $user;
            }
        }
        return null;
    }
}
?>