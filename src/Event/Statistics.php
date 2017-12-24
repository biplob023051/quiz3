<?php
namespace App\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class Statistics implements EventListenerInterface {

	/**
     * ImplementedEvents method.
     *
     * @return array
     */
	public function implementedEvents() 
	{
		return [
			'Model.Users.login' => 'userLogin',
            'Model.Quizzes.quiz_create' => 'quizCreate'
		];
	}

	public function userLogin(Event $event) {
		$this->Statistics = TableRegistry::get('Statistics');
        $data = [
            'user_id' => $event->data['user_id'],
            'type' => 'user_login',
            'created' => date("Y-m-d H:i:s"),
        ];
        $entity = $this->Statistics->newEntity($data);
        $this->Statistics->save($entity);
        return true;
	}

    public function quizCreate(Event $event) {
        $this->Statistics = TableRegistry::get('Statistics');
        $data = [
            'user_id' => $event->data['user_id'],
            'type' => 'quiz_create',
            'created' => date("Y-m-d H:i:s"),
        ];
        $entity = $this->Statistics->newEntity($data);
        $this->Statistics->save($entity);
        return true;
    }
} 