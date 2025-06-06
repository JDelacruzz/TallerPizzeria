<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderExtraIngredient;

class OrderExtraIngredientController extends Controller
{
    private function getOrderExtraIngredients()
    {
        $orderExtraIngredients = DB::table('order_extra_ingredient')
            ->select([
                'order_extra_ingredient.*',
                'users.name as client_name',
                'extra_ingredients.name as extra_ingredient_name'
            ])
            ->join('orders', 'order_extra_ingredient.order_id', '=', 'orders.id')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->join('extra_ingredients', 'order_extra_ingredient.extra_ingredient_id', '=', 'extra_ingredients.id')
            ->get();
        return $orderExtraIngredients;
    }

    private $validationRules = [
        'order_id' => 'required|exists:orders,id',
        'extra_ingredient_id' => 'required|exists:extra_ingredients,id',
        'quantity' => 'required|integer|min:1'
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderExtraIngredients = $this->getOrderExtraIngredients();

        return json_encode($orderExtraIngredients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), $this->validationRules);
        if ($validate->fails()) {
            return response()->json([
                'msg' => 'Se produjo un error en la validación de la información.',
                'statusCode' => 400
            ]);
        }

        $orderExtraIngredient = new OrderExtraIngredient();
        $orderExtraIngredient->order_id = $request->input('order_id');
        $orderExtraIngredient->extra_ingredient_id = $request->input('extra_ingredient_id');
        $orderExtraIngredient->quantity = $request->input('quantity');
        $orderExtraIngredient->save();

        $orderExtraIngredients = $this->getOrderExtraIngredients();
        return json_encode($orderExtraIngredients);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderExtraIngredient = OrderExtraIngredient::find($id);

        if (is_null($orderExtraIngredient)) {
            return abort(404);
        }

        return json_encode($orderExtraIngredient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), $this->validationRules);
        if ($validate->fails()) {
            return response()->json([
                'msg' => 'Se produjo un error en la validación de la información.',
                'statusCode' => 400
            ]);
        }

        $orderExtraIngredient = OrderExtraIngredient::find($id);

        if (is_null($orderExtraIngredient)) {
            return abort(404);
        }

        $orderExtraIngredient->order_id = $request->input('order_id');
        $orderExtraIngredient->extra_ingredient_id = $request->input('extra_ingredient_id');
        $orderExtraIngredient->quantity = $request->input('quantity');
        $orderExtraIngredient->save();

        $orderExtraIngredients = $this->getOrderExtraIngredients();
        return json_encode($orderExtraIngredients);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderExtraIngredient = OrderExtraIngredient::find($id);

        if (is_null($orderExtraIngredient)) {
            return abort(404);
        }

        $orderExtraIngredient->delete();

        $orderExtraIngredients = $this->getOrderExtraIngredients();
        return json_encode($orderExtraIngredients);
    }
}
