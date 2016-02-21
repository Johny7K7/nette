<?php
/**
 * Created by PhpStorm.
 * User: johny
 * Date: 21.2.2016
 * Time: 16:22
 */

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form;


class PostPresenter extends Nette\Application\UI\Presenter
{

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderShow($postId)
    {
        $this->template->post = $this->database->table('posts')->get($postId);
    }
}