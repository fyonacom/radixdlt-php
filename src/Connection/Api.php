<?php

namespace Techworker\RadixDLT\Connection;

use Techworker\RadixDLT\Connection;

class Api
{
    public function __construct(
        protected Connection $connection,
        protected string $uri)
    {
    }

    protected function httpGet(string $uri) : string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function universe() {
        $res = $this->httpGet($this->uri . '/universe');
        return json_decode($res, true);
    }
    public function tasksWaiting() {
        $res = $this->httpGet($this->uri . '/system/modules/api/tasks-waiting');
        return json_decode($res, true)['count'];
    }
    public function websockets() {
        $res = $this->httpGet($this->uri . '/system/modules/api/websockets');
        return json_decode($res, true)['count'];
    }
    public function network() {
        $res = $this->httpGet($this->uri . '/network');
        return json_decode($res, true);
    }
}
