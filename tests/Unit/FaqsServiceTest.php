<?php

namespace Tests\Unit;

use App\Models\BlogNew;
use App\Services\FaqsService;
use App\Services\Task\FaqService;
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

    public function test_index()
    {
        (new FaqService)->index();
        $this->assertTrue(true);
    }

    public function test_faqAll()
    {
        $faqId = 1;
        (new FaqService)->faqAll($faqId);
        $this->assertTrue(true);
    }

    public function test_get_all()
    {
        (new FaqService)->get_all();
        $this->assertTrue(true);
    }

    public function test_get_key()
    {
        $key = 'site.title';
        (new FaqService)->get_key($key);
        $this->assertTrue(true);
    }
}
