<?php
namespace CHevereto\Core;

use Symfony\Component\EventDispatcher\Event;

class Listener
{
    public function onDemoEvent(Event $event)
    {
        // fetch event information here
        echo "Listener is called!\n";
        echo "The value of the foo is: ".$event->getFoo()."\n";
    }
}
