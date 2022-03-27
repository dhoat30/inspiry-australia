<?php

require_once dirname(__FILE__) . '/Processes/AbstractProcess.php';
require_once dirname(__FILE__) . '/Processes/CancelQuote.php';
require_once dirname(__FILE__) . '/Processes/Refund.php';
require_once dirname(__FILE__) . '/Processes/CreateOrder/CompatibilityMode/Process.php';
require_once dirname(__FILE__) . '/Processes/CreateOrder/CreateOrderAbstract.php';
require_once dirname(__FILE__) . '/Processes/CreateOrder/WC_LT_3_6/Process.php';
require_once dirname(__FILE__) . '/Processes/CreateOrder/WC_GT_3_6/Process.php';
require_once dirname(__FILE__) . '/Processes/CreateQuote/CreateQuoteAbstract.php';
require_once dirname(__FILE__) . '/Processes/CreateQuote/WC_LT_3_6/Process.php';
require_once dirname(__FILE__) . '/Processes/CreateQuote/WC_GT_3_6/Process.php';
require_once dirname(__FILE__) . '/Processes/RedirectPayment/Process.php';
require_once dirname(__FILE__) . '/Processes/RedirectPayment/CompatibilityMode/Process.php';

class Laybuy_ProcessManager
{
    private static $instance;

    private $apiGateway;
    private $wcGateway;
    private $compatibilityMode;

    public function setApiGateway($gateway)
    {
        $this->apiGateway = $gateway;
        return $this;
    }

    public function getApiGateway()
    {
        return $this->apiGateway;
    }

    public function setWcGateway($gateway)
    {
        $this->wcGateway = $gateway;
        return $this;
    }

    public function getWcGateway()
    {
        return $this->wcGateway;
    }

    public function setCompatibilityMode($mode)
    {
        $this->compatibilityMode = $mode;
        return $this;
    }

    public function getCompatibilityMode()
    {
        return $this->compatibilityMode;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function createOrderFromQuote($quote_id)
    {
        if (version_compare( WC_VERSION, '3.6', '<' )) {
            $process = new Laybuy_Processes_CreateOrder_WC_LT_3_6_Process();
        } else {
            $process = new Laybuy_Processes_CreateOrder_WC_GT_3_6_Process();
        }

        $process->setProcessManager($this);
        $process->setQuoteId($quote_id);
        return $process->execute();
    }

    public function createQuote($checkout)
    {
        if (version_compare( WC_VERSION, '3.6', '<' )) {
            $process = new Laybuy_Processes_CreateQuote_WC_LT_3_6_Process();
        } else {
            $process = new Laybuy_Processes_CreateQuote_WC_GT_3_6_Process();
        }

        $process->setProcessManager($this);
        $process->setCheckout($checkout);
        $process->execute();
    }

    public function createOrder($orderId)
    {
        $process = new Laybuy_Processes_CreateOrder_CompatibilityMode_Process();

        $process->setOrderId($orderId);
        $process->setProcessManager($this);
        $process->execute();
    }

    public function refund($orderId, $amount, $reason)
    {
        $process = new Laybuy_Processes_Refund();

        $process->setProcessManager($this)
            ->setOrderId($orderId)
            ->setAmount($amount)
            ->setReason($reason);

        return $process->execute();
    }

    public function cancelQuote($quoteId, $status = 'cancelled')
    {
        $process = new Laybuy_Processes_CancelQuote();
        return $process->setQuoteId($quoteId)
            ->setStatus($status)
            ->execute();
    }

    public function processRedirectPayment($id, $status, $token)
    {
        if ($this->compatibilityMode) {
            $process = new Laybuy_Processes_RedirectPayment_CompatibilityMode_Process();
            $process->setOrderId($id);
        } else {
            $process = new Laybuy_Processes_RedirectPayment_Process();
            $process->setQuoteId($id);
        }

        return $process
            ->setStatus($status)
            ->setToken($token)
            ->setProcessManager($this)
            ->execute();
    }
}