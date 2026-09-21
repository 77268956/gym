<?php

namespace App\Http\Controllers;

use App\Models\CanjeEcogim;
use App\Models\ProductoEcogim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoEcogimController extends Controller
{
    public function index()
    {
        $productos = ProductoEcogim::withTrashed()
            ->withCount('canjes')
            ->orderBy('estado')
            ->orderBy('nombre')
            ->get();

        $categorias = ProductoEcogim::whereNull('deleted_at')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria');

        $totalCanjes = CanjeEcogim::count();
        $stockTotal = ProductoEcogim::where('estado', 'activo')->sum('stock');
        $totalProductos = ProductoEcogim::whereNull('deleted_at')->count();

        return view('admin.productos.index', compact(
            'productos',
            'categorias',
            'totalCanjes',
            'stockTotal',
            'totalProductos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'categoria' => 'nullable|string|max:50',
            'puntos_valor' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('productos_ecogim', 'public');
        }

        ProductoEcogim::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'puntos_valor' => $request->puntos_valor,
            'stock' => $request->stock,
            'estado' => $request->estado,
            'imagen' => $imagenPath,
        ]);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, ProductoEcogim $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'categoria' => 'nullable|string|max:50',
            'puntos_valor' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'descripcion', 'categoria', 'puntos_valor', 'stock', 'estado']);

        if ($request->boolean('eliminar_imagen') && $producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
            $data['imagen'] = null;
        } elseif ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos_ecogim', 'public');
        }

        $producto->update($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(ProductoEcogim $producto)
    {
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }

    public function toggleStatus(ProductoEcogim $producto)
    {
        $producto->update([
            'estado' => $producto->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return redirect()->route('admin.productos.index')->with('success', 'Estado actualizado.');
    }
}
