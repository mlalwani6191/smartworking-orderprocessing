<?php
namespace Smartworking\CustomOrderProcessing\Api;

interface OrderProcessingInterface
{
    /**
     * Function to change order status via API
     *
     * @param mixed $request
     * @return string
     */
    public function changeStatus(mixed $request);
}
