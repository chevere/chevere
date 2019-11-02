<?php

final class MyClass
{
    public $public;
    public $publicProp;

    protected $protected;
    protected $protectedProp;

    private $private;
    private $privateProp;


    public function __construct()
    {
        $this->public = 'public';
        $this->publicProp = 'publicProp';
        $this->protected = 'protected';
        $this->protectedProp = 'protectedProp';
        $this->private = 'private';
        $this->privateProp = 'privateProp';
    }
}

$myObject = new MyClass();
$serial = serialize($myObject);
$unserial = unserialize($serial);

var_dump([
  'Original object' => $myObject,
  'Serialized' => $serial,
  'Unserialized' => $unserial,
  'Compare' => $myObject == $unserial,
]);
