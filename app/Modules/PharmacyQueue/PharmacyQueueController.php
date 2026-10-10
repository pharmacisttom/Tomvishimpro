<?php

namespace Tomvis\HIMPro\Modules\PharmacyQueue;

use Tomvis\HIMPro\Core\Controller\BaseController;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;
use Tomvis\HIMPro\Core\Response\JsonResponse;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;

class PharmacyQueueController extends BaseController
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function index(Request $request): Response
    {
        return $this->view('pharmacy_queue.index');
    }

    public function tvDisplay(Request $request): Response
    {
        return $this->view('pharmacy_queue.tv_display');
    }

    public function listApi(Request $request): JsonResponse
    {
        // Sample active queues for PDH Pharmacy Queue
        $queues = [
            ['id' => 1, 'queue_no' => 'A001', 'hn' => '67000001', 'patient_name' => 'สมชาย ใจดี', 'counter' => 1, 'status' => 'Calling', 'created_at' => date('H:i:s', strtotime('-10 mins'))],
            ['id' => 2, 'queue_no' => 'A002', 'hn' => '67000002', 'patient_name' => 'สมศรี มีสุข', 'counter' => 2, 'status' => 'Dispensing', 'created_at' => date('H:i:s', strtotime('-8 mins'))],
            ['id' => 3, 'queue_no' => 'B001', 'hn' => '67000003', 'patient_name' => 'วันดี บุญมี', 'counter' => null, 'status' => 'Waiting', 'created_at' => date('H:i:s', strtotime('-5 mins'))],
            ['id' => 4, 'queue_no' => 'B002', 'hn' => '67000004', 'patient_name' => 'กิตติ เพชรรัตน์', 'counter' => null, 'status' => 'Waiting', 'created_at' => date('H:i:s', strtotime('-2 mins'))],
        ];

        return $this->jsonSuccess($queues, 'Active Pharmacy Queues retrieved.');
    }

    public function generateQueue(Request $request): JsonResponse
    {
        $hn = (string)$request->input('hn', '67000001');
        $type = (string)$request->input('queue_type', 'A'); // A = General, B = Special/Elderly

        $num = mt_rand(10, 99);
        $queueNo = "{$type}0{$num}";

        return $this->jsonSuccess([
            'queue_no' => $queueNo,
            'hn' => $hn,
            'queue_type' => $type,
            'status' => 'Waiting',
            'created_at' => date('Y-m-d H:i:s'),
        ], "Pharmacy Queue Ticket [{$queueNo}] generated!");
    }

    public function callQueue(Request $request): JsonResponse
    {
        $queueNo = (string)$request->input('queue_no', 'A001');
        $counter = (int)$request->input('counter', 1);

        return $this->jsonSuccess([
            'queue_no' => $queueNo,
            'counter' => $counter,
            'status' => 'Calling',
            'called_at' => date('H:i:s'),
        ], "Queue [{$queueNo}] called to Counter {$counter}!");
    }
}
