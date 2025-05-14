<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CodigoRecuperacionMail;

class UserController extends Controller 
{
    public function getAuthenticatedUser(Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    }
    public function mostrarUser() {
        $users = User::all();

        if ($users->isEmpty()) {
            $data = [
                'message' => 'No se encontraron usuarios',
                'status' => 200
            ];
            return response()->json($data, 404);
        }

        $data = [
            'users' => $users,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function mostrarUnSoloUser($id) {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'user' => $user,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function verificarEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:255|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422); // Código 422 para validaciones
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['message' => 'Correo encontrado'], 200); // Correo EXISTE
        }

        return response()->json(['error' => 'Correo no encontrado'], 404);
    }

    public function crearUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255|unique:users,email',
            'password' => ['required','string','min:8','max:16','regex:/[a-zA-Z]/','regex:/[0-9]/','regex:/[\W]/'],
            'rol' => 'required|string|in:Cliente,Vendedor'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol' => $request->rol
        ]);

        if (!$user) {
            $data = [
                'message' => 'Error al crear el usuario',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'user' => $user,
            'status' => 201
        ];

        return response()->json($data, 201);
    }

    public function loginUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:255|exists:users,email',
            'password' => ['required','string','max:16','regex:/[a-zA-Z]/','regex:/[0-9]/','regex:/[\W]/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
                'status' => 401
            ], 401);
        }

        $token = $user->createToken('AppToken')->plainTextToken;

        // Definir ruta según el rol
        $redirectTo = $user->rol === 'Vendedor' ? '/admin' : '/';

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
            'token' => $token,
            'redirect_to' => $redirectTo,
            'status' => 200
        ], 200);
    }


    public function enviarCode(Request $request) {
        $request->validate(['email' => 'required|email|string|max:255|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $data = [
                'message' => 'Correo electrónico no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // Generar un código aleatorio de 5 dígitos
        $codigo = random_int(10000, 99999);

        // Guardar el código en la base de datos (puedes usar un campo específico o una tabla de recuperación)
        $user->recovery_code = $codigo;
        $user->code_expires_at = now()->addMinutes(10); // Establece un tiempo de expiración
        $user->save();

        // Enviar el código por correo electrónico
        Mail::to($user->email)->send(new CodigoRecuperacionMail($codigo));

        return response()->json(['message' => 'Código enviado al correo electrónico']);
    }

    public function verificarCode(Request $request) {
        $request->validate([
            'email' => 'required|email|string|max:255|exists:users,email',
            'code' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $data = [
                'message' => 'Correo electrónico no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // Verificar que el código sea correcto y no haya expirado
        if ($user->recovery_code != $request->code || $user->code_expires_at < now()) {
            return response()->json(['error' => 'Código inválido o expirado'], 400);
        }

        // Código válido, restablecer el recovery_code para evitar reutilización
        $user->recovery_code = null;
        $user->code_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Código verificado correctamente'], 200);
    }

    public function verificarPassword(Request $request) {
        // Validar que los campos email y password estén presentes
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:255|exists:users,email',
            'password' => ['required','string','min:8','max:16','regex:/[a-zA-Z]/','regex:/[0-9]/','regex:/[\W]/'],
        ]);

        // Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $data = [
                'message' => 'Correo electrónico no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // Verificar que la contraseña sea correcta
        if (Hash::check($request->password, $user->password)) {
            $data = [
                'message' => 'La contraseña es igual a la existente',
                'status' => 401
            ];
            return response()->json($data, 401);
        }

        $data = [
            'message' => 'Contraseña permitida',
            'user' => $user,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function cambiarPassword(Request $request) {
        // Validar que los campos email y password estén presentes
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:255|exists:users,email',
            'password' => ['required','string','min:8','max:16','regex:/[a-zA-Z]/','regex:/[0-9]/','regex:/[\W]/'],
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        // Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $data = [
                'message' => 'Correo electrónico no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $user->password = bcrypt($request->password);

        $user->save();

        $data = [
            'message' => 'Contraseña actualizado',
            'user' => $user,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function modificarUser(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $data = $request->all();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuario actualizado', 'user' => $user], 200);
    }
 
    public function modificarCampoUser(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|string|max:255|unique:users,email,' . $id,
            'adress' => 'string',
            'password' => ['string','min:8','max:16','regex:/[a-zA-Z]/','regex:/[0-9]/','regex:/[\W]/'],
            'rol' => 'string|in:Cliente,Vendedor',
            'numero_tarjeta' => 'string|min:16|max:16|unique:users,numero_tarjeta,' . $id,
            'nombre_titular' => 'string|max:255|unique:users,nombre_titular,' . $id,
            'cvv' => 'string|min:3|max:3',
            'fecha_vencimiento' => 'string'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('adress')) {
            $user->adress = $request->adress;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('rol')) {
            $user->rol = $request->rol;
        }

        if ($request->has('numero_tarjeta')) {
            $user->numero_tarjeta = $request->numero_tarjeta;
        }

        if ($request->has('nombre_titular')) {
            $user->nombre_titular = $request->nombre_titular;
        }

        if ($request->has('cvv')) {
            $user->cvv = $request->cvv;
        }

        if ($request->has('fecha_vencimiento')) {
            $user->fecha_vencimiento = $request->fecha_vencimiento;
        }

        $user->save();

        $data = [
            'message' => 'Usuario actualizado',
            'user' => $user,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function eliminarUser($id) {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $user->delete();

        $data = [
            'message' => 'Usuario eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
