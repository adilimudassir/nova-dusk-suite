<?php

namespace Tests\Browser;

use App\Role;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\IndexComponent;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActionFieldTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function actions_can_receive_and_utilize_field_input()
    {
        $this->seed();

        $user = User::find(1);
        $role = factory(Role::class)->create();
        $user->roles()->attach($role);

        $this->browse(function (Browser $browser) use ($user, $role) {
            $browser->loginAs(User::find(1))
                    ->visit(new Pages\Detail('users', 1))
                    ->within(new IndexComponent('roles'), function ($browser) {
                        $browser->clickCheckboxForId(1)
                                ->runAction('update-pivot-notes', function ($browser) {
                                    $browser->type('@notes', 'Custom Notes');
                                });
                    });

            $this->assertEquals('Custom Notes', $user->fresh()->roles->first()->pivot->notes);
        });
    }
}
