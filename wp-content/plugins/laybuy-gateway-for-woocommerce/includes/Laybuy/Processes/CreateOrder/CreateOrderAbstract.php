<?php

abstract class Laybuy_Processes_CreateOrder_CreateOrderAbstract extends Laybuy_Processes_AbstractProcess
{
    abstract public function setQuoteId($quoteId);
    abstract public function getQuoteId();
}