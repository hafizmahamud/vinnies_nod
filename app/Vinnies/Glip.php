<?php

namespace App\Vinnies;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class Glip
{
    protected $url      = 'https://hooks.glip.com/webhook/60bbadde-c572-4f5b-8d1d-5a402a37d1ea';
    protected $title    = '';
    protected $body     = '';
    protected $activity = '';

    protected $client;

    public function __construct($url = false)
    {
        if ($url) {
            $this->url = $url;
        }

        $this->client = new Client([
            'debug' => false,
        ]);
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

    public function send($args = [])
    {
        $title    = $this->title ?? null;
        $body     = $this->body;
        $activity = $this->activity ?? null;

        $query = array_merge(['icon' => 'https://media.tumblr.com/tumblr_m0pk61hM3X1r5a7i5.jpg'], compact('title', 'body', 'activity'));
        $query = array_filter($query);

        $this->client->post($this->url, ['json' => $query]);
    }
}
