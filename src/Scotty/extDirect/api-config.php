<?php

/**
 * http://www.sencha.com/products/extjs/extdirect
 * Configuration based on ext js example.
 * The namespace key was added in order to be able to use namespaces.
 * len: amount of arguments
 * Note from the documentation: "The PHP implementation does not need
 * to know any additional information about the arguments, their name,
 * their type or their order. Other server-side implementations may need
 * to know this information and will have to store it in the configuration."
 */
$API = array(
    'QueryDatabase' => array(
        'namespace' => 'Scotty\\logviewer\\',
        'methods' => array(
            'getResults' => array(
                'len' => 1
            )
        )
    ),
    'Authentication' => array(
        'namespace' => 'Scotty\\auth\\',
        'methods' => array(
            'login' => array(
                'len' => 0,
                'formHandler' => true // handle form on server with Ext.Direct.Transaction
                        )
        )
    ),
    'ManageUser' => array(
        'namespace' => 'Scotty\\user\\',
        'methods' => array(
            'register' => array(
                'len' => 0,
                'formHandler' => true
            )
        )
    ),
    'SessionInfoProvider' => array(
        'namespace' => 'Scotty\\session\\',
        'methods' => array(
            'getCurrentLiederbuchId' => array(
                'len' => 0
            ),
            'setCurrentLiederbuchId' => array(
                'len' => 1
            )
        )
    ),
    'ChangeOrder' => array(
        'namespace' => 'Scotty\\song\\',
        'methods' => array(
            'moveUp' => array(
                'len' => 2
            ),
            'moveDown' => array(
                'len' => 2
            )
        )
    )
);
?>