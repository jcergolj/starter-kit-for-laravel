## General code instructions

- Don't generate code comments above the methods or code blocks if they are obvious. Generate comments only for something that needs extra explanation for the reasons why that code was written

---

## PHP instructions

- In PHP, use `match` operator over `switch` whenever possible
- Use PHP 8 constructor property promotion. Don't create an empty Constructor method if it doesn't have any parameters.
- Using Services in Controllers: if Service class is used only in ONE method of Controller, inject it directly into that method with type-hinting. If Service class is used in MULTIPLE methods of Controller, initialize it in Constructor.
- Use return types in functions whenever possible, adding the full path to classname to the top in `use` section

---

## Laravel instructions

- For DB pivot tables, use correct alphabetical order, like "project_role" instead of "role_project"
- I am using Laravel Valet locally, so always assume that the main URL of the project is `http://[folder_name].test`
- When generating Controllers, put validation in Form Request classes
- Aim for "slim" Controllers and put larger logic pieces in Service classes
- Use Laravel section classes instead of helpers section classes whenever possible. Examples: use `Auth::id()` instead of `auth()->id()` and adding `Auth` in the `use` section.

---

## Use Laravel 11+ skeleton structure

- **Service Providers**: there are no other service providers except AppServiceProvider. Don't create new service providers unless absolutely necessary. Use Laravel 11+ new features, instead. Or, if you really need to create a new service provider, register it in `bootstrap/providers.php` and not `config/app.php` like it used to be before Laravel 11.
- **Event Listeners**: since Laravel 11, Listeners auto-listen for the events if they are type-hinted correctly.
- **Console Scheduler**: scheduled commands should be in `routes/console.php` and not `app/Console/Kernel.php` which doesn't exist since Laravel 11.
- **Middleware**: whenever possible, use Middleware by class name in the routes. But if you do need to register Middleware alias, it should be registered in `bootstrap/app.php` and not `app/Http/Kernel.php` which doesn't exist since Laravel 11.
- **Tailwind**: in new Blade pages, use Tailwind and not Bootstrap, unless instructed otherwise in the prompt. Tailwind is already pre-configured since Laravel 11, with Vite.
- **Faker**: in Factories, use `fake()` helper instead of `$this->faker`.

---

## Testing instructions

Use PHPUnit. Run tests with `php artisan test`.

If factory entity is used in multiple places create a private property. If in the test you need to change do e.g. `$this->user->update(['' => ''])` first thing in the test method. In each test use `#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\AllocatedTokenController::class)]
#[\PHPUnit\Framework\Attributes\CoversMethod(\App\Http\Controllers\AllocatedTokenController::class, 'index')]`.
When testing controllers split feature tests by method.

In the Arrange phase, use Laravel factories but add meaningful column values and variable names if they help to understand failed tests better.
Bad example: `$user1 = User::factory()->create();`
Better example: `$adminUser = User::factory()->create(['email' => 'admin@admin.com'])`;

In the Assert phase, perform these assertions when applicable:
- HTTP status code returned from Act: `assertStatus()`
- Structure/data returned from Act (Blade or JSON): functions like `assertViewHas()`, `assertSee()`, `assertDontSee()` or `assertJsonContains()`
- Or, redirect assertions like `assertRedirect()` and `assertSessionHas()` in case of Flash session values passed
- DB changes if any create/update/delete operation was performed, do the following fetch new values from DB e.g. using ->fresh and them use `assertSame`

Check existing tests as templates if they exists.
