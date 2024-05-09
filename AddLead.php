<?php

class AddLead
{
    private $url = 'https://crm.belmar.pro/api/v1/addlead';
    private $token = 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';

    public function addLead($firstName, $lastName, $phone, $email)
    {
        $validator = $this->validateInput($firstName, $lastName, $phone, $email);

        if (!$validator['success']) {
            return json_encode($validator);
        }
        
        $data = $this->prepareData($firstName, $lastName, $phone, $email);

        $response = $this->sendRequest($data);

        return $this->handleResponse($response);
    }

    private function validateInput($firstName, $lastName, $phone, $email)
    {
        return match (true) {
            empty($firstName) || strlen($firstName) > 255 => ['success' => false, 'message' => 'Invalid first name'],
            empty($lastName) || strlen($lastName) > 255 => ['success' => false, 'message' => 'Invalid last name'],
            empty($phone) || strlen($phone) > 255 => ['success' => false, 'message' => 'Invalid phone'],
            empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) => ['success' => false, 'message' => 'Invalid email'],
            default => ['success' => true],
        };
    }

    private function prepareData($firstName, $lastName, $phone, $email)
    {
        return [
            "firstName" => $firstName,
            "lastName" => $lastName,
            "phone" => $phone,
            "email" => $email,
            "box_id" => "28",
            "offer_id" => "5",
            "countryCode" => "GB",
            "language" => "en",
            "password" => "qwerty12",
            "ip" => $_SERVER['REMOTE_ADDR'],
            "landingUrl" => $_SERVER['HTTP_HOST']
        ];
    }

    private function sendRequest($data)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'token: ' . $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function handleResponse($response)
    {
        $result = json_decode($response, true);

        if ($result && $result['status'] === true) {
            $responseData = ['success' => true, 'message' => 'Lead added successfully', 'data' => $result];
        } else {
            $responseData = ['success' => false, 'message' => 'Failed to add lead', 'data' => $result];
        }

        header('Content-Type: application/json');
        return json_encode($responseData);
    }
}

// Використання класу
$addLead = new AddLead();
$result = $addLead->addLead($_POST['firstName'], $_POST['lastName'], $_POST['phone'], $_POST['email']);
echo ($result);

?>