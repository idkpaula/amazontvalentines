<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\ValoracionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CompraController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user(); // Devuelve el usuario autenticado si se ha hecho login vía Sanctum
// });

// RUTAS SOBRE LOS USUARIOS
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getAuthenticatedUser']);
Route::post('/user/Create', [UserController::class, 'crearUser']); // RUTA PARA EL REGISTER
Route::post('/user/Login', [UserController::class, 'loginUser']); // RUTA PARA EL LOGIN
Route::put('/user/Modify/{id}', [UserController::class, 'modificarUser']); // RUTA PARA EDITAR EL PERFIL DEL USUARIO
Route::delete('/user/Delete/{id}', [UserController::class, 'eliminarUser']); // RUTA PARA ELIMINAR EL USUARIO / ELIMINAR LA CUENTA DE AMAZON'T

// RUTAS SOBRE LAS OPINIONES
Route::post('/opinion/Create', [OpinionController::class, 'crearOpinion']); // Crea una nueva opinión sobre un producto
Route::patch('/opinion/Update/{id}', [OpinionController::class, 'actualizarOpinion']); // Actualiza una opinión existente
Route::delete('/opinion/Delete/{id}', [OpinionController::class, 'eliminarOpinion']); // Elimina una opinión
Route::get('/opinion/ForProduct/{id}', [OpinionController::class, 'OpinionPorProducto']); // Obtiene opiniones de un producto específico
Route::get('/opinion/ForUser/{id}', [OpinionController::class, 'OpinionPorUsuario']); // Obtiene opiniones realizadas por un usuario

// RUTAS SOBRE LAS VALORACIONES
Route::post('/review/Create', [ValoracionController::class, 'crearValoracion']); // Crea una nueva valoración (puntuación) de producto
Route::patch('/review/Update/{id}', [ValoracionController::class, 'actualizarValoracion']); // Actualiza una valoración existente
Route::delete('/review/Delete/{id}', [ValoracionController::class, 'eliminarValoracion']); // Elimina una valoración
Route::get('/review/ForProduct/{id}', [ValoracionController::class, 'ValoracionesPorProducto']); // Obtiene todas las valoraciones de un producto
Route::get('/review/ForUser/{id}', [ValoracionController::class, 'ValoracionesPorUsuario']); // Obtiene todas las valoraciones hechas por un usuario
Route::get('/review/Average/{id}', [ValoracionController::class, 'promedioPuntuacionProducto']); // Obtiene la puntuación promedio de un producto

// RUTAS SOBRE LAS CATEGORÍAS
Route::get('/category/All', [CategoriaController::class, 'mostrarCategory']); // RUTA PARA MOSTRAR TODAS LAS CATEGORIAS
Route::get('/category/Only/{id}', [CategoriaController::class, 'mostrarUnSoloCategory']); // RUTA PARA MOSTRAR UNA CATEGORIA EN ESPECIFICO
Route::post('/category/Create', [CategoriaController::class, 'crearCategory']); // RUTA PARA CREAR UNA CATEGORIA
Route::put('/category/Modify/{id}', [CategoriaController::class, 'modificarCategory']); // RUTA PARA MODIFICAR UNA CATEGORIA
Route::delete('/category/Delete/{id}', [CategoriaController::class, 'eliminarCategory']); // RUTA PARA ELIMINAR UNA CATEGORIA

// RUTAS SOBRE LOS PRODUCTOS
Route::get('/product/All', [ProductoController::class, 'mostrarProduct']); // RUTA PARA MOSTRAR TODOS LOS PRODUCTOS INDISTINTAMENTE DE SU CATEGORIA
Route::get('/product/Only/{id}', [ProductoController::class, 'mostrarUnSoloProduct']); // RUTA PARA MOSTRAR UN PRODUCTO EN ESPECÍFICO
Route::post('/product/Create', [ProductoController::class, 'crearProduct']); // RUTA PARA CREAR UN NUEVO PRODUCTO
Route::put('/product/Modify/{id}', [ProductoController::class, 'modificarProduct']); // RUTA PARA MODIFICAR UN PRODUCTO
Route::delete('/product/Delete/{id}', [ProductoController::class, 'eliminarProduct']); // RUTA PARA ELIMINAR EL PRODUCTO
Route::get('/product/ProdForCat', [ProductoController::class, 'productPorCategoria']); // RUTA PARA MOSTRAR TODOS LOS PRODUCTOS AGRUPADOS POR CATEGORIAS
Route::get('/product/ProdSale', [ProductoController::class, 'productPorOferta']); // Obtiene productos en oferta
Route::get('/product/ProdNotSale', [ProductoController::class, 'productSinOferta']); // Obtiene productos que no están en oferta
Route::get('/product/ProdForOnlyCat/{id}', [ProductoController::class, 'productPorUnaCategoria']); // RUTA PARA OBTENER LOS PRODUCTOS DE UNA CATEGOORIA EN ESPECÍFICO

// RUTAS SOBRE EL CARRITO
Route::get('/cart/My/{id}', [CarritoController::class, 'mostrarCart']); // RUTA PARA MOSTRARLE AL USUARIO SU CARRITO
Route::post('/cart/Add', [CarritoController::class, 'agregarCart']); // RUTA PARA AGREGAR UN PRODUCTO AL CARRITO
Route::patch('/cart/increase/{id}', [CarritoController::class, 'aumentarCantidad']); // RUTA PARA AUMENTAR LA CANTIDAD DE UN PRODUCTO EN EL CARRITO
Route::patch('/cart/decrease/{id}', [CarritoController::class, 'disminuirCantidad']); // RUTA PARA DISMINUIR LA CANTIDAD DE UN PRODUCTO EN EL CARRITO
Route::delete('/cart/delete/{id}', [CarritoController::class, 'eliminarCart']); // RUTA PARA ELIMINAR UN PRODUCTO DEL CARRITO
Route::get('/cart/Total/{id}', [CarritoController::class, 'totalCart']); // Obtiene el total a pagar del carrito
Route::post('/cart/Confirm/{id}', [CarritoController::class, 'confirmarCompra']); // Confirma la compra de los productos del carrito

// RUTAS SOBRE LAS COMPRAS
Route::get('/shop/Buy/{id}', [CompraController::class, 'mostrarCompra']); // RUTA PARA EL HISTORIAL DE PEDIDOS EN EL USER-PROFILE

// Ruta de prueba para saber si la API está bien conectada o no a Angular
Route::get('/prueba', function () {
    return response()->json(['mensaje' => 'Conexión exitosa desde Laravel']);
});