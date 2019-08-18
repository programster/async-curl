<?php

/*
 * This is like an ENUM and solely exists to prevent typos and mistakes with entering strings.
 * This could be considered OTT.
 */

namespace Programster\AsyncCurl;

class Method
{
    private $m_methodString;

    private function __construct($methodString)
    {
        $this->m_methodString = $methodString;
    }

    public static function createGet() { return new Method('get'); }
    public static function createHead() { return new Method('head'); }
    public static function createPost() { return new Method('post'); }
    public static function createPut() { return new Method('put'); }
    public static function createPatch() { return new Method('patch'); }
    public static function createDelete() { return new Method('delete'); }


    public function __toString() { return $this->m_methodString; }
}

