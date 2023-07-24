<?php

namespace Tests\Unit;

use App\Models\BlogNew;
use App\Services\FaqsService;
use Tests\TestCase;

class FaqsServiceTest extends TestCase
{
    public function test_questions()
    {
        $faq_id = 7;
        (new FaqsService)->questions($faq_id);
        $this->assertTrue(true);
    }

    public function test_getFaqCategories()
    {
        (new FaqsService)->getFaqCategories();
        $this->assertTrue(true);
    }

    public function test_blog_news_index()
    {
        (new FaqsService)->blog_news_index();
        $this->assertTrue(true);
    }

    public function test_blog_news_show()
    {
        $newsId = 34;
        (new FaqsService)->blog_news_show($newsId);
        $this->assertTrue(true);
    }

    public function test_news()
    {
        $blog_news = BlogNew::find(34);
        (new FaqsService)->news($blog_news);
        $this->assertTrue(true);
    }
}
