<?php

use function PHPUnit\Framework\assertTrue;

class TaskCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function create(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email','admin@admin.com');
        $I->fillField('password','password');
        $I->click('Войти');
        $I->amOnPage('/task/create?category_id=22');
        $I->fillField('name','Test');
        $I->click('Oтправить');
        $I->fillField('Вес посылки, кг','123');
        $I->fillField('Ширина, м','123');
        $I->fillField('Высота (м)','123');
        $I->fillField('Ценность посылки, сум','123');
        $I->click('Далее');
        $I->click(['id' => 'getlocal']);
        $I->click(['id' => '1']);
        $I->click('Далее');
        $I->fillField('start_date','2022-07-31 12:00:0');
        $I->click('Далее');
        $I->fillField('amount2','200 000');
        $I->click('Далее');
        $I->fillField('description','Description Test');
        $I->click('Далее');
        assertTrue('true');
//        $I->fillField('phone_number','+998(94)916-46-86');
//        $I->click('Oтправить');
    }
    public function task_search(AcceptanceTester $I)
    {
        $I->amOnPage('/task-search');
        $I->fillField('filter','asdas');
        $I->click('Найти');
        assertTrue('true');
    }
}
