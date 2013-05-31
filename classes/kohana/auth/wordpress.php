<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Authentication module for Wordpress.
 * 
 * Wordpress has a cookie-based authentication method. It stores credentials in
 * a cookie called wordpress_logged_in_$hash where $hash is a hash signature of 
 * ...
 * 
 * @package Auth
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
class Kohana_Auth_Wordpress extends Auth {

    const CONTRIBUTOR = 'contributor',
            ADMINISTRATOR = 'contributor',
            EDITOR = 'contributor';

    protected function _login($username, $password, $remember) {

        $wp_user = ORM::factory('wp_user', array('user_login' => $username));

        if ($wp_user->user_pass === $this->hash($password)) {

            $this->complete_login($wp_user);

            if ($remember === TRUE) {
                // Set autologin cookie
            }
        }
    }

    public function complete_login($user) {

        $cookie = $user->user_login . '|' . time() . '|' . $this->hash($user->password);

        Cookie::set('wordpress_logged_in_e8f685e8858949ab1a159751bcb13b5b', $cookie);
    }

    public function logout($destroy = FALSE, $logout_all = FALSE) {

        Cookie::delete('wordpress_logged_in_e8f685e8858949ab1a159751bcb13b5b');

        return !$this->logged_in();
    }

    public function check_password($password) {

        if (!$this->logged_in()) {
            return FALSE;
        }

        return $this->hash($this->get_user()->password) === $password;
    }

    /**
     * Role is Editor, Contributor, Author ...
     * @param type $role
     */
    public function logged_in($role = NULL) {

        $role = ORM::factory('wp_role');

        return parent::logged_in() ? $this->get_user()->has('roles', $role) : FALSE;
    }

    public function password($username) {

        $wp_user = ORM::factory('wp_user', array('user_login' => $username));

        return $wp_user->loaded() ? $wp_user->user_pass : FALSE;
    }

    public function get_user($default = NULL) {

        $parts = explode('|', Arr::get($_COOKIE, 'wordpress_logged_in_e8f685e8858949ab1a159751bcb13b5b'));

        if (count($parts) !== 3) {
            return $default;
        }

        list($username, $timestamp, $password) = $parts;

        if (!$this->check_password($password)) {
            return $default;
        }

        $wp_user = ORM::factory('wp_user', array('user_login' => $username));

        return $wp_user->loaded() ? $wp_user : $default;
    }

}

?>
