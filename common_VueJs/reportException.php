<?php

// class that extend exception to report default error
class reportException extends Exception
{
    public function reportError()
    {
        switch ($this->getCode()) {
            case 500:
                $data['code'] = $this->getCode();
                $data['state'] = 'Internal Server Error';
                $data['message'] = $this->getMessage();
                break;

            case 406:
                $data['code'] = $this->getCode();
                $data['state'] = 'Not Acceptable';
                $data['message'] = $this->getMessage();
                $data['url'] = '../autenticazione_VueJs';
                break;

            case 401:
                $data['code'] = $this->getCode();
                $data['state'] = 'Unauthorized';
                $data['message'] = $this->getMessage();
                break;

            case 400:
                $data['code'] = $this->getCode();
                $data['state'] = 'Bad Request';
                $data['message'] = $this->getMessage();
                break;

            default:
                $data['code'] = $this->getCode();
                $data['state'] = 'Error';
                $data['message'] = $this->getMessage();
                break;
        }
        echo json_encode($data);
        exit;
    }
}