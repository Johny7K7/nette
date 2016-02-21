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
        $post = $this->database->table('posts')->get($postId);
        if (!$post) {
            $this->error('Stranka nebola najdena');
        }
        $this->template->post = $post;
        $this->template->comments = $post->related('comment')->order('created_at DESC');
    }

    protected function createComponentCommentForm()
    {
        $form = new Form;

        $form->addText('name','Meno:')
            ->setRequired();

        $form->addText('email','Email:')
            ->setRequired();

        $form->addTextArea('content','Komentar:')
            ->setRequired();

        $form->addSubmit('send','Publikovat komentar');

        $form->onSuccess[] = array($this, 'commentFormSucceeded');

        return $form;
    }

    public function commentFormSucceeded($form, $values)
    {
        $postId = $this->getParameter('postId');

        $this->database->table('comments')->insert(array(
            'post_id' => $postId,
            'name' => $values->name,
            'email' => $values->email,
            'content' => $values->content,
        ));

        $this->flashMessage('Dakujem za komentar','success');
        $this->redirect('this');
    }

    protected function createComponentPostForm()
    {
        $form = new Form;
        $form->addText('title', 'Titulok:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();
        $form->addSubmit('send', 'Ulozit a publikovat');
        $form->onSuccess[] = array($this, 'postFormSucceeded');

        return $form;
    }

    public function postFormSucceeded($form, $values)
    {
        $postId = $this->getParameter('postId');

        if ($postId) {
            $post = $this->database->table('posts')->get($postId);
            $post->update($values);
        } else {
            $post = $this->database->table('posts')->insert($values);
        }

        $this->flashMessage("Prispevok bol uspesne publikovany.", 'success');
        $this->redirect('show', $post->id);
    }

    public function actionEdit($postId)
    {
        $post = $this->database->table('posts')->get($postId);
        if (!$post)  {
            $this->error('Prispevok nebol najdeny.');
        }
        $this['postForm']->setDefaults($post->toArray());
    }
}