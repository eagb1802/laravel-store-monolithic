<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    # Lista como BD
    protected $list = [
        ['id' => 1, 'name' => 'Pepsi', 'price' => 1.50],
        ['id' => 2, 'name' => 'Franela', 'price' => 0.99],
        ['id' => 3, 'name' => 'Fresas', 'price' => 0.50],
    ];

    // Se ejecuta al momento de iniciar el servidor
    public function __construct()
    {
        // Creando una cache para el endpoint de productos
        $this->list = Cache::store('file')->get('products', $this->list);
    }

    private function saveList()
    {
        Cache::store('file')->put('products', $this->list);
    }

    // http://127.0.0.1:8000
    // http://localhost:8000

    // Obtener todos los productos
    public function getAll()
    {
        return response()->json([
            'message' => 'Lista de productos',
            'items' => $this->list,
        ]);
    }

    // '1' (string) == 1 (int) = true
    // '1' (string) === 1 (int) = false
    // '1' (string) === '1' (int) = true

    // Obtener productos por id `utilizando PATH PARAM`
    public function findById(string $id)
    {
        foreach ($this->list as $item) {

            if (strval($item['id']) === $id) {
                return response()->json([
                    'message' => 'Producto encontrado',
                    'item' => $item
                ]);
            }
        }

        return response()->json([
            'message' => 'Producto no encontrado',
            'item' => null
        ], 404);
    }


    // Obtener productos por nombre `utilizando QUERY PARAM`
    public function findByName(Request $request)
    {
        // Obtener nombre del producto que llega por `query param`
        $name = $request->query('name', '');

        $filtered = [];
        foreach ($this->list as $item) {

            // Filtrar productos por nombre
            if (str_starts_with(strtolower($item['name']), strtolower($name))) {
                array_push($filtered, $item);
            }
        }

        if (count($filtered) > 0) {
            return response()->json([
                'message' => 'Productos encontrados: ' . count($filtered),
                'items' => $filtered
            ]);
        } else {
            return response()->json([
                'message' => 'Productos encontrados: ' . count($filtered),
                'items' => $filtered
            ], 404);
        }
    }

    // Insertar nuevo producto
    public function insert(Request $request)
    {
        // Obtener los valores del request
        $name = $request->input('name', '');
        $price = $request->input('price');

        // Validar que el producto no exista
        foreach ($this->list as $item) {

            if (strtolower($item['name']) === strtolower($name)) {
                return response()->json([
                    'message' => 'El producto ya existe'
                ], 400);
            }
        }

        // Generar id unico
        $nextId = $this->list[count($this->list) - 1]['id'] + 1;

        // Crear product
        $newProduct = [
            'id' => $nextId,
            'name' => $name,
            'price' => doubleval($price)
        ];

        // Insertar nuevo producto
        array_push($this->list, $newProduct);

        // Toma un snapshot de la `list` y la guarda en cache
        $this->saveList();

        return response()->json([
            'message' => 'Producto creado con exito',
            'item' => $newProduct
        ]);
    }

    public function update(Request $request, string $id)
    {
        foreach ($this->list as $index => $item) {

            //echo ($id);

            if (strval($item['id']) === $id) {

                $this->list[$index]['name'] = $request->input('name');
                $this->list[$index]['price'] = $request->input('price');

                $this->saveList();
                return response()->json([
                    'message' => 'Producto actualizado',
                    'item' => $this->list[$index]
                ]);
            }
        }

        return response()->json([
            'message' => 'Producto no encontrado',
            'item' => null
        ], 404);
    }

    public function delete(string $id)
    {
        foreach ($this->list as $index => $item) {

            //echo ($id);

            if (strval($item['id']) === $id) {

                //$product_deleted = $this->list[$index];

                array_slice($this->list, $index, 1);

                $this->saveList();

                return response()->json([
                    'message' => 'Producto eliminado',
                    'item' => null //$product_deleted
                ]);
            }
        }

        return response()->json([
            'message' => 'Producto no encontrado',
            'item' => null
        ], 404);
    }
}
