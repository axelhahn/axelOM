<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class FirstCest
{
    public function frontpageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->see('Willkommen bei Axels ObjManager');
        $I->dontSee('Error');
        $I->dontSee('Fehler');
    }
}
              