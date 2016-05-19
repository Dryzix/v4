<?php

namespace PHX\Core\Auth;

class Auth{

    public function __construct()
    {
        session_start();
    }

    public function set($key, $val){
        $_SESSION['user'][$key] = $val;
        return $this;
    }

    public function setAdmin(){
        $_SESSION['userIsAdmin'] = true;
        return $this;
    }

    public function isAdmin(){
        return isset($_SESSION['userIsAdmin']) ? $_SESSION['userIsAdmin'] : false;
    }

    public function get($key){
        return isset($_SESSION['user'][$key]) ? $_SESSION['user'][$key] : false;
    }

    public function addToken($token)
    {
        $value = md5(rand(0,1000).time().microtime());
        $_SESSION['token'][$token] = $value;

        return $value;
    }

    public function isTokenValid($token, $value, $destroy = true)
    {
        if(isset($_SESSION['token'][$token]) && $_SESSION['token'][$token] == $value)
        {
            if($destroy)
            {
                unset($_SESSION['token'][$token]);
            }

            return true;
        }

        return false;
    }

    public function destroy(){
        session_destroy();
    }
}