<?php

namespace App\Singleton;

class Component
{
    private $url;
    private $type;
    public $path;

    public function __construct()
    {
        $this->path = "components.";
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function link()
    {
        $link = view($this->path . "links." . $this->type, [
            "url" => url($this->url),
        ]);

        return $link->render();
    }

    public static function build()
    {
        return new Component();
    }
}
