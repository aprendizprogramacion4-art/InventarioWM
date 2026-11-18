<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_log_in_and_access_inventory_operations(): void
    {
        User::create([
            'name' => 'Empleado de prueba',
            'username' => 'employee.one',
            'password' => 'employee-secret',
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'username' => 'employee.one',
            'password' => 'employee-secret',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('inventory_role', 'employee')
            ->assertSessionMissing('admin_authenticated');

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('movements.index'))->assertOk();
        $this->get(route('reports.index'))->assertOk();
    }

    public function test_employee_cannot_manage_products_or_users(): void
    {
        User::create([
            'name' => 'Empleado de prueba',
            'username' => 'employee.two',
            'password' => 'employee-secret',
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->withSession([
            'inventory_authenticated' => true,
            'inventory_role' => 'employee',
            'inventory_username' => 'employee.two',
        ]);

        $this->post(route('products.store'), [
            'name' => 'Producto no autorizado',
            'stock' => 0,
            'minimum_stock' => 1,
            'buy_price' => 100,
            'sale_price' => 150,
        ])->assertForbidden();

        $this->get(route('users.index'))->assertForbidden();
        $this->assertDatabaseMissing('products', ['name' => 'Producto no autorizado']);
    }

    public function test_employee_can_register_a_movement_and_the_username_is_audited(): void
    {
        $product = Product::create([
            'name' => 'Producto de empleado',
            'category' => 'Pruebas',
            'stock' => 4,
            'minimum_stock' => 1,
            'buy_price' => 100,
            'sale_price' => 150,
        ]);

        $this->withSession([
            'inventory_authenticated' => true,
            'inventory_role' => 'employee',
            'inventory_username' => 'employee.audit',
        ])->post(route('movements.store'), [
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 3,
            'notes' => 'Ingreso registrado por empleado',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 7]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'created_by' => 'employee.audit',
            'quantity' => 3,
        ]);
    }

    public function test_admin_can_create_an_employee_account_with_a_hashed_password(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('users.store'), [
                'name' => 'Nuevo empleado',
                'username' => 'new.employee',
                'password' => 'employee-secret',
                'password_confirmation' => 'employee-secret',
            ])
            ->assertRedirect(route('users.index'));

        $user = User::where('username', 'new.employee')->firstOrFail();

        $this->assertSame('employee', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('employee-secret', $user->password));
        $this->assertNotSame('employee-secret', $user->password);
    }

    public function test_inactive_employee_cannot_log_in(): void
    {
        User::create([
            'name' => 'Empleado inactivo',
            'username' => 'inactive.employee',
            'password' => 'employee-secret',
            'role' => 'employee',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'username' => 'inactive.employee',
            'password' => 'employee-secret',
        ])->assertSessionHasErrors('username')
          ->assertSessionMissing('inventory_authenticated');
    }

    public function test_employee_cannot_delete_another_user(): void
    {
        $user = User::create([
            'name' => 'Empleado',
            'username' => 'employee.three',
            'password' => 'employee-secret',
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->withSession([
            'inventory_authenticated' => true,
            'inventory_role' => 'employee',
        ])->delete(route('users.destroy', $user))->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
