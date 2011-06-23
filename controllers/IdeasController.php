<?php

namespace app\controllers;

use app\models\Ideas;

class IdeasController extends \lithium\action\Controller {

	protected $_admins = array('gwoo' => true);

	public function index() {
		$latest = Ideas::all(array('order' => array('date' => 'desc')));
		$popular = Ideas::all(array('order' => array('score' => 'desc')));
		return compact('latest', 'popular');
	}

	public function add() {
		if (!$this->request->user) {
			return $this->redirect('Login::index');
		}
		$idea = Ideas::create();
		$idea->user = $this->request->user;
		$idea->date = time();

		if (($this->request->data) && $idea->save($this->request->data)) {
			return $this->redirect(array('Ideas::index'));
		}
		return compact('idea');
	}

	public function vote() {
		if (!$this->request->user) {
			return $this->redirect('Login::index');
		}
		$idea = Ideas::find($this->request->id);

		if (isset($idea->voters[$this->request->user])) {
			return $this->redirect('Ideas::index');
		}
		$idea->score = $idea->score + 1;
		$idea->voters = array_merge((array) $idea->voters, array($this->request->user => true));

		if ($idea && $idea->save()) {
			//success maaybe flash a message
		}
		return $this->redirect('Ideas::index');
	}

	public function delete() {
		if (isset($this->_admins[$this->request->user])) {
			Ideas::find($this->request->id)->delete();
		}
		return $this->redirect('Ideas::index');
	}
}

?>