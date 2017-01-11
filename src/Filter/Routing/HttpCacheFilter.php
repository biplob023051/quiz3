<?php
namespace App\Routing\Filter;

use Cake\Event\Event;
use Cake\Routing\DispatcherFilter;

class HttpCacheFilter extends DispatcherFilter
{

    public function afterDispatch(Event $event)
    {
        $request = $event->data['request'];
        $response = $event->data['response'];

        if ($response->statusCode() === 200) {
            $response->sharable(true);
            $response->expires(strtotime('+1 day'));
        }
    }
}