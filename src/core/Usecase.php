<?php

namespace Core;

abstract class Usecase
{
    abstract public function perform(): Result;
}
