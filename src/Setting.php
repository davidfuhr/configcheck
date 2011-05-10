<?php

interface Setting
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return scalar
     */
    public function getValue();
}

