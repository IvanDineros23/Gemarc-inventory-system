<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;

class DeliveryReviewController extends Controller
{
    // List DR groups for review
    public function index()
    {
        $rows = DB::table('deliveries')
            ->selectRaw("COALESCE(dr_number, '') as dr_number, COALESCE(customer,'') as customer, COALESCE(dr_date, MIN(date)) as dr_date, COUNT(*) as item_count, SUM(qty * COALESCE(unit_cost,0)) as total_amount,
                SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as rejected_count,
                MIN(id) as sample_id, GROUP_CONCAT(DISTINCT COALESCE(remarks,'' ) SEPARATOR ' | ') as remarks_preview
            ")
            ->groupBy('dr_number','customer','dr_date')
            ->orderBy('dr_date','desc')
            ->get();

        return view('pages.delivery-review', ['groups' => $rows]);
    }

    // Return details for a DR number (JSON)
    public function details($drNumber)
    {
        $rows = Delivery::where('dr_number', $drNumber)->with('product')->get();
        return response()->json(['rows' => $rows]);
    }

    // details by sample id (for groups where dr_number is NULL)
    public function detailsBySampleId($id)
    {
        $sample = Delivery::find($id);
        if (!$sample) {
            return response()->json(['rows' => []]);
        }

        if ($sample->dr_number) {
            $rows = Delivery::where('dr_number', $sample->dr_number)->with('product')->get();
            return response()->json(['rows' => $rows]);
        }

        // find deliveries that match same customer and dr_date (or date)
        $query = Delivery::whereNull('dr_number')
            ->where(function($q) use ($sample) {
                if ($sample->customer) {
                    $q->where('customer', $sample->customer);
                }
                if ($sample->dr_date) {
                    $q->whereDate('dr_date', $sample->dr_date);
                } else {
                    $q->whereDate('date', $sample->date);
                }
            });

        $rows = $query->with('product')->get();
        return response()->json(['rows' => $rows]);
    }

    // Approve or reject a DR (sets all rows with dr_number)
    public function approve(Request $request, $drNumber)
    {
        $this->validate($request, ['approved' => 'required|in:0,1']);
        $approved = (int) $request->approved;

        $now = now();
        $userId = Auth::id();

        $affected = Delivery::where('dr_number', $drNumber)->update([
            'is_approved' => $approved,
            'approved_by' => $userId,
            'approved_at' => $now,
        ]);

        return response()->json(['updated' => $affected]);
    }
}
