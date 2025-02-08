<?php

namespace App\Event;

use App\Model\News;

abstract class AEventNews implements IEvent
{
    protected ?News $news;

    public function __construct(?News $news)
    {
        $this->news = $news;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }
}
