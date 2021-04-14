<?php

namespace App\Controllers;

use \Core\View;

/**
 * MainMenu controller (example)
 *
 * PHP version 7.3
 */
class MainMenu extends Authenticated
{
    /**
     * MainMenu index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('MainMenu/index.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
