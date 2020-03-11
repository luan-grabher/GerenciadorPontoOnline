<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagarmeObjectAttribute extends Model
{
    protected string $name;
    protected array $routeFor;
    protected $default;

    /**
     * PagarmeObjectAttribute constructor.
     * @param string $name
     * @param array $routeFor
     * @param $default
     */
    public function __construct(string $name, array $routeFor, $default = "")
    {
        $this->name = $name;
        $this->routeFor = $routeFor;
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getRouteFor(): array
    {
        return $this->routeFor;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

}
