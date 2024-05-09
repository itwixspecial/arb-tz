<?php

    // Фасад для спрощення доступу до функціональності фільтрації та відправки запиту до API
    class LeadStatusesFacade {
        public static function getLeadStatuses($from_date, $to_date) {
            $filter = new LeadFilter();
            $filter->setDateRange($from_date, $to_date);
            $response = $filter->applyFilter();

            return $response;
        }
    }

    // Клас для фільтрації даних
    class LeadFilter {
        private $from_date;
        private $to_date;

        public function setDateRange($from_date, $to_date) {
            $this->from_date = $from_date;
            $this->to_date = $to_date;
        }

        public function applyFilter() {
            $url = 'https://crm.belmar.pro/api/v1/getstatuses';
            $token = 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';

            $data = [
                'date_from' => $this->from_date,
                'date_to' => $this->to_date,
                'page' => 0,
                'limit' => 100
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'token: ' . $token,
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
    }

    // Функція для обробки запиту і відправки відповіді
    function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["from_date"]) && isset($_POST["to_date"])) {
            $from_date = $_POST["from_date"];
            $to_date = $_POST["to_date"];

            $response = LeadStatusesFacade::getLeadStatuses($from_date, $to_date);

            echo $response;
        } else {
            echo json_encode(['error' => 'Missing parameters']);
        }
    }

    // Виклик функції обробки запиту
    handleRequest();

?>