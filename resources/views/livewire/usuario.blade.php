<?php
namespace App\Livewire\Forms;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;
use App\Livewire\Forms\PostForm;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;

new class extends Component {
    //
    use WithPagination;

    #[Validate('required|min:5')]
    public $password = '';
    public $password_confirmation = '';

    public $userRole = null;
    public $usuarioSeleccionado;

    public function with(): array
    {
        return [
            'users' => User::paginate(),
            'roles' => Role::all(),
        ];
    }

    public function save()
    {
        $this->validate();
    }

    public function seleccionarUsuario($userId)
    {
        $this->usuarioSeleccionado = $userId;
    }

    public function cambiarContraseña()
    {
        // Lógica para cambiar la contraseña del usuario
        // Aquí puedes implementar la lógica para actualizar la contraseña del usuario
    }

    public function asignarRol()
    {
        // Validación básica
        if (!$this->usuarioSeleccionado || !$this->userRole) {
            //$this->dispatchBrowserEvent('mostrar-error', ['message' => 'Debe seleccionar un usuario y un rol.']);
            return;
        }

        $user = User::find($this->usuarioSeleccionado);

        if (!$user) {
            //$this->dispatchBrowserEvent('mostrar-error', ['message' => 'Usuario no encontrado.']);
            return;
        }

        // Reemplazar rol existente por el nuevo
        $user->syncRoles([$this->userRole]);

        // Cerrar modal vía JS
        //$this->dispatchBrowserEvent('cerrar-modal');

        // Mensaje flash opcional
        session()->flash('message', 'Rol asignado correctamente.');

        // Limpiar datos
        $this->reset(['usuarioSeleccionado', 'userRole']);

        // Opcional: recargar usuarios si los tienes en una propiedad $users
        // $this->cargarUsuarios();
    }
};
?>

<div>
    <div class="table-wrapper table-responsive">
        <table class="table striped-table">
            <thead>
                <tr>
                    <th>
                        <h6>Nombre</h6>
                    </th>
                    <th>
                        <h6>Apellidos</h6>
                    </th>
                    <th>
                        <h6>Correo</h6>
                    </th>
                    <th>
                        <h6>Rol</h6>
                    </th>
                    <th class="text-end">
                        <h6>Acciones</h6>
                    </th>
                </tr>
                <!-- end table row-->
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <p>{{ $user->nombre }}</p>
                        </td>
                        <td>
                            <p>{{ $user->apellido_paterno }} {{ $user->apellido_materno }}</p>
                        </td>
                        <td>
                            <p>{{ $user->email }}</p>
                        </td>
                        <td>
                            <p>{{ $user->roles->first()->name ?? 'Sin rol' }}</p>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    ⋮
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#modalCambiarPassword">
                                            Cambiar Contraseña
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#modalAsignarRol"
                                            wire:click.prevent="seleccionarUsuario({{ $user->id }})">
                                            Asignar Rol
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#">
                                            Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <!-- end table row -->
            </tbody>
        </table>
        <!-- end table -->
        {{ $users->links() }}
    </div>

    <div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<!-- ID del usuario -->">
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAsignarRol" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="asignarRol">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<!-- ID del usuario -->">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Rol</label>
                            <select class="form-select" wire:model="userRole">
                                <option value="">-- Selecciona un rol --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
