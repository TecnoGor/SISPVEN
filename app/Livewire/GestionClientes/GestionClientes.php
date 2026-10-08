<?php

namespace App\Livewire\GestionClientes;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Rules\CodigosTelefono;
use App\Models\Documento;
use App\Models\UsuarioSeguimiento;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GestionClientes extends Component
{
    public $search = '';
    public $cliente_id = null; 

    public $form_editar = [ 
        'open' => false,
        'nombre' => '',
        'apellido' => '',
        'tipo_documento' => 'V',
        'numero_documento' => '',
        'telefono' => '',
        'correo' => '',
    ];

    protected function rules()
    {
         // Obtener tipos de documento desde la BD
        $tipos_documentos = Documento::pluck('tipo')->toArray();

        // Si no hay registros, forzamos un error claro
        if (empty($tipos_documentos)) {
            $tipos_documentos = []; // evita errores en Rule::in
        }
        
        return [
            // Nombre y Apellido: Solo letras, mínimo 3 caracteres.
            'form_editar.nombre' => [
                'required', 
                'min:3', 
                'max:50', 
                'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u'
            ],
            'form_editar.apellido' => [
                'required', 
                'min:3', 
                'max:50', 
                'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u'
            ],

            'form_editar.tipo_documento' => [
                'required',
                Rule::in($tipos_documentos),
            ],
            
            // Cédula: Solo números, entre 7 y 8 caracteres
            'form_editar.numero_documento' => [
                'required',
                'numeric',
                'digits_between:7,8',
                Rule::unique('clientes', 'numero_documento')->ignore($this->cliente_id, 'cliente_id'),
            ],
            
            // Teléfono:
            'form_editar.telefono' => [
                'required',
                new CodigosTelefono(),
                Rule::unique('clientes', 'telefono')->ignore($this->cliente_id, 'cliente_id'),
            ],
            
            // Correo: Formato estricto
            'form_editar.correo' => [
                'required',
                'email',
                'regex:/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/'
            ],
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form_editar.nombre' => 'nombre',
            'form_editar.apellido' => 'apellido',
            'form_editar.tipo_documento' => 'tipo de documento',
            'form_editar.numero_documento' => 'cédula o documento',
            'form_editar.telefono' => 'número de teléfono',
            'form_editar.correo' => 'correo electrónico',
        ];
    }

    protected function messages()
    {
        return [
            'form_editar.nombre.regex' => 'El nombre debe contener solo letras.',
            'form_editar.apellido.regex' => 'El apellido debe contener solo letras.',
            'form_editar.numero_documento.digits_between' => 'La cédula debe tener entre 7 y 8 números.',
            'form_editar.correo.regex' => 'El correo debe tener un formato válido.',
            'form_editar.telefono.App\Rules\CodigosTelefono' => 'El campo teléfono debe ser un número telefónico válido (nacional o internacional).',
        ];
    }

    public function edit(Cliente $cliente)
    {
        $this->resetValidation();
        $this->cliente_id = $cliente->cliente_id;
        
        // Sincronizamos los datos
        $this->form_editar = array_merge($this->form_editar, $cliente->only([
            'nombre', 'apellido', 'tipo_documento', 'numero_documento', 'telefono', 'correo'
        ]));
        
        $this->form_editar['open'] = true;
    }

    public function update()
    {
        $this->validate();

        $cliente = Cliente::find($this->cliente_id);

        if (!$cliente) {
            $this->dispatch('alertSuccess2', message: 'Cliente no encontrado');
            return;
        }

        $cliente->update(collect($this->form_editar)->except('open')->toArray());

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se actualizó el cliente ({$cliente->cliente_id})",
        ]);

        $this->reset(['form_editar', 'cliente_id']);
        $this->resetValidation();

        $this->dispatch('alertSuccess', message: '¡Cliente actualizado!');
    }


    public function render()
    {
        $clientes = [];
        if (!empty($this->search)) {
            $clientes = Cliente::where('numero_documento', 'like', "%{$this->search}%")
                ->orWhere('nombre', 'like', "%{$this->search}%")
                ->orWhere('apellido', 'like', "%{$this->search}%")
                ->get();
        }
        $documentos = Documento::orderBy('tipo')->get();
        return view('livewire.gestion-clientes.gestion-clientes', compact('clientes', 'documentos'));
    }
}