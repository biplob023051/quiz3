<?php
namespace App\Routing\Filter;

use Cake\Event\Event;
use Cake\Routing\DispatcherFilter;

class RoutingExtendFilter extends DispatcherFilter
{

    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        if (empty($request->url)) {
        	if ($request->session()->read('Auth.User')){
	            $request->params['controller'] = 'Quizzes';
	            $request->params['action'] = 'index';
	        } else { 
	            $request->params['controller'] = 'Pages';
	            $request->params['action'] = 'index';
	        }
        } 
    }
}