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
        dump(get_class($this));
    }
}

$provider = new \Helium\EventDispatcher\ListenerProvider([
    function (FirstEvent $event) {
        dump(FirstEvent::class);
    },
    function (SecondEvent $event) {
        dump(SecondEvent::class);
    },
    new InvokableListener(),
]);

$provider->initListeners([
    new InvokableListener(),
]);

$eventDispatcher = new \Helium\EventDispatcher\EventDispatcher($provider);

$eventDispatcher->dispatch(new ThirdEvent());
$eventDispatcher->dispatch(new FirstEvent());
$eventDispatcher->dispatch(new SecondEvent());
