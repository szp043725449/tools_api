<?php

namespace Tools\Api\Sign;

interface ControllerSignInterface
{
    public function getSecret($appId);
}