<?php

namespace DdB\StuartApiBundle;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    public function __construct(string $privateKey, string $publicKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }
}