<?php

namespace DdB\StuartApiBundle;

use http\Exception\InvalidArgumentException;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    public function __construct(string $privateKey, string $publicKey)
    {
        if(!$privateKey || !$publicKey){
            throw new InvalidArgumentException("Please provide a public and a private key to use this bundle");
        }
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }
}