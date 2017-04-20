<?php

return array(
    'user' => array(
        'create' => array(
                'email' => array('required', 'email', 'unique:users'),
                'pass' => array('required', 'min:6', 'max:255', 'confirmed'),
                'username' => array('required', 'min:6', 'max:255', 'alpha_num', 'unique:users'),
                'last_name' => array('min:3', 'max:255', 'alpha'),
                'first_name' => array('min:3', 'max:255', 'alpha'),
                'eQsl_call' => array('min:4','max:12','alfa_num'),
                'latitude' => array('nummeric'),
                'longitude' => array('nummeric'),
                'grid' => array('min:4','max:6','alfa_num'),
            ),
        'update' => array(
            'email' => array('required', 'email'),
            'pass' => array('min:6', 'max:255'),
            'username' => array('required', 'min:3', 'max:255', 'alpha_num'),
            'last_name' => array('min:3', 'max:255', 'alpha'),
            'first_name' => array('min:3', 'max:255', 'alpha'),
            ),
        'login' => array(
                'username' => array('required', 'min:3', 'max:255', 'alpha_num'),
                'email' => array('required', 'email'),
                'pass' => array('required', 'min:6', 'max:255'),
            ),
    ),
    'group' => array(
        'groupname' => array('required', 'min:3', 'max:16', 'alpha'),
    ),
    'permission' => array(
        'name' => array('required', 'min:3', 'max:100'),
        'value' => array('required', 'alpha_dash', 'min:3', 'max:100'),
        'description' => array('required', 'min:3', 'max:255')
    ),
);