<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class FoodTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $food;


    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create(['name' => 'Admin Utama']);
        Storage::fake('public');
        $this->food = Food::factory()->create([
            'name' => 'Makanan Lama',
            'price' => 10000,
            'image' => 'makanan-lama.jpg'
        ]);
        Storage::disk('public')->put('images/makanan-lama.jpg', 'file-lama');
        $this->withoutVite();
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ayam Goreng',
            'price' => 20000,
            'image' => UploadedFile::fake()->image('ayam-goreng.jpg')
        ], $overrides);
    }

    public function test_guest_cannot_view_food_page()
    {
        $response = $this->get(route('admin.view.food'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_food_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->get(route('admin.view.food'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_food_page_with_no_search()
    {
        Food::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.food'));

        $response->assertOk();
        $response->assertViewIs('admin.food.index');
        $response->assertViewHas('search', '');
        $response->assertViewHas('foods', function ($foods) {
            return $foods->total() === 6 &&
                $foods->count() === 3;
        });
    }

    public function test_admin_can_view_food_page_with_search_results()
    {
        Food::factory()->create(['name' => 'Nasi Goreng Spesial']);
        Food::factory()->create(['name' => 'Ayam Bakar Madu']);
        Food::factory()->create(['name' => 'Soto Ayam Lamongan']);
        Food::factory()->create(['name' => 'Gado-gado']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.view.food', ['search' => 'Ayam']));

        $response->assertOk();
        $response->assertViewHas('search', 'Ayam');
        $response->assertViewHas('foods', function ($foods) {
            return $foods->total() === 2;
        });
        $response->assertSee('Ayam Bakar Madu');
        $response->assertSee('Soto Ayam Lamongan');
        $response->assertDontSee('Nasi Goreng Spesial');
    }

    public function test_admin_gets_validation_error_for_non_ascii_search()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.view.food'))
            ->get(route('admin.view.food', ['search' => '€£¥']));

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHasErrors('search');
    }

    public function test_guest_cannot_view_create_food_page()
    {
        $response = $this->get(route('admin.create.food'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_create_food_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->get(route('admin.create.food'));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_create_food_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.create.food'));

        $response->assertOk();
        $response->assertViewIs('admin.food.create');
    }

    public function test_guest_cannot_store_food()
    {
        $response = $this->post(route('admin.store.food'), $this->getValidData());
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_store_food()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->post(route('admin.store.food'), $this->getValidData());
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_store_new_food_successfully()
    {
        $data = $this->getValidData();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('foods', [
            'name' => 'Ayam Goreng',
            'price' => 20000,
            'image' => 'ayam-goreng.jpg'
        ]);

        Storage::disk('public')->assertExists('images/ayam-goreng.jpg');
    }

    public function test_food_name_is_required()
    {
        $data = $this->getValidData(['name' => '']);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('name');
    }

    public function test_food_price_must_be_a_valid_number()
    {
        $data = $this->getValidData(['price' => '1.5']);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('price');
    }

    public function test_food_price_must_be_at_least_1()
    {
        $data = $this->getValidData(['price' => 0]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('price');
    }

    public function test_food_image_is_required()
    {
        $data = $this->getValidData(['image' => null]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('image');
    }

    public function test_food_image_must_be_an_image()
    {
        $data = $this->getValidData([
            'image' => UploadedFile::fake()->create('bukan-gambar.pdf', 100)
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('image');
    }

    public function test_food_image_must_be_under_2mb()
    {
        $data = $this->getValidData([
            'image' => UploadedFile::fake()->image('gambar-besar.jpg')->size(3000)
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.create.food'))
            ->post(route('admin.store.food'), $data);

        $response->assertRedirect(route('admin.create.food'));
        $response->assertSessionHasErrors('image');
    }

    public function test_guest_cannot_view_edit_food_page()
    {
        $food = Food::factory()->create();
        $response = $this->get(route('admin.edit.food', $food));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_view_edit_food_page()
    {
        $user = User::factory()->create();
        $food = Food::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->get(route('admin.edit.food', $food));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_view_edit_food_page()
    {
        $food = Food::factory()->create();
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.edit.food', $food));

        $response->assertOk();
        $response->assertViewIs('admin.food.edit');
        $response->assertViewHas('food', function ($viewFood) use ($food) {
            return $viewFood->id === $food->id;
        });
    }

    public function test_admin_gets_404_if_food_not_found()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.edit.food', 999));

        $response->assertNotFound();
    }

    public function test_guest_cannot_update_food()
    {
        $response = $this->patch(route('admin.update.food', $this->food), [
            'name' => 'Nama Baru'
        ]);
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_update_food()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->patch(route('admin.update.food', $this->food), [
                'name' => 'Nama Baru'
            ]);
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_update_food_without_changing_image()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.update.food', $this->food), [
                'name' => 'Makanan Baru',
                'price' => 15000,
            ]);

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHas('success');

        $this->food->refresh();
        $this->assertEquals('Makanan Baru', $this->food->name);
        $this->assertEquals(15000, $this->food->price);
        $this->assertEquals('makanan-lama.jpg', $this->food->image);
        Storage::disk('public')->assertExists('images/makanan-lama.jpg');
    }

    public function test_admin_can_update_food_with_a_new_image()
    {
        $file = UploadedFile::fake()->image('gambar-baru.png');

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.update.food', $this->food), [
                'name' => 'Makanan Super Baru',
                'price' => 20000,
                'image' => $file,
            ]);

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHas('success');

        $this->food->refresh();
        $this->assertEquals('Makanan Super Baru', $this->food->name);
        $this->assertEquals('makanan-super-baru.png', $this->food->image);
        Storage::disk('public')->assertExists('images/makanan-super-baru.png');
    }

    public function test_admin_gets_validation_error_when_updating_with_no_name()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.edit.food', $this->food))
            ->patch(route('admin.update.food', $this->food), [
                'name' => '',
                'price' => 15000,
            ]);

        $response->assertRedirect(route('admin.edit.food', $this->food));
        $response->assertSessionHasErrors('name');
    }

    public function test_admin_gets_validation_error_when_updating_with_invalid_new_image()
    {
        $file = UploadedFile::fake()->create('dokumen.pdf', 100);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.edit.food', $this->food))
            ->patch(route('admin.update.food', $this->food), [
                'name' => 'Makanan Gagal',
                'price' => 15000,
                'image' => $file,
            ]);

        $response->assertRedirect(route('admin.edit.food', $this->food));
        $response->assertSessionHasErrors('image');
    }

    public function test_guest_cannot_delete_food()
    {
        $food = Food::factory()->create();
        $response = $this->delete(route('admin.destroy.food', $food));
        $response->assertRedirect(route('view.login'));
    }

    public function test_user_cannot_delete_food()
    {
        $user = User::factory()->create();
        $food = Food::factory()->create();
        $response = $this->actingAs($user, 'user')
            ->delete(route('admin.destroy.food', $food));
        $response->assertRedirect(route('view.login'));
    }

    public function test_admin_can_delete_food_and_its_image()
    {
        $food = Food::factory()->create(['image' => 'gambar-tes.jpg']);
        Storage::disk('public')->put('images/gambar-tes.jpg', 'file-palsu');
        Storage::disk('public')->assertExists('images/gambar-tes.jpg');

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.destroy.food', $food));

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('foods', ['id' => $food->id]);
        Storage::disk('public')->assertMissing('images/gambar-tes.jpg');
    }

    public function test_admin_can_delete_food_even_if_image_is_missing_from_storage()
    {
        $food = Food::factory()->create(['image' => 'gambar-hilang.jpg']);
        Storage::disk('public')->assertMissing('images/gambar-hilang.jpg');

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.destroy.food', $food));

        $response->assertRedirect(route('admin.view.food'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('foods', ['id' => $food->id]);
    }
}
