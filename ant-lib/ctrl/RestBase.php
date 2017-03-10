<?php

namespace ctrl;

abstract class RestBase extends Base
{
    abstract public function GET();

    abstract public function POST();

    abstract public function PUT();

    abstract public function DELETE();
}