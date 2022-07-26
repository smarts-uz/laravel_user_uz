<?php

class UserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function login(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email','admin@admin.com');
        $I->fillField('password','password');
        $I->click('Войти');
    }
}
