<?php

namespace Test;

require 'vendor/autoload.php';

class FirstEvent
{
    private $isTest = true;
}

class SecondEvent extends FirstEvent {}

class ThirdEvent {}

class InvokableListener
{
    public function __invoke(FirstEvent $event)
    {
        dump(sprintf("-- Call in %s --", get_class($this)));
    }
}

$provider = new \Helium\EventDispatcher\ListenerProvider\ListenerProvider([
    function (FirstEvent $event) {
        dump("-- Call in FirstEvent --");
    },
    function (SecondEvent $event) {
        dump("-- Call in SecondEvent --");
    },
    new InvokableListener(),
]);

$eventDispatcher = new \Helium\EventDispatcher\EventDispatcher($provider);

$eventDispatcher->dispatch(new ThirdEvent());
$eventDispatcher->dispatch(new FirstEvent());
$eventDispatcher->dispatch(new SecondEvent());
