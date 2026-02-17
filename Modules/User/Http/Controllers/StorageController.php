<?php

namespace Modules\User\Http\Controllers;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StorageController extends Controller
{
    public function index() {
        // Business Owners and Admins only
        auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Business Owner']) || abort(403);

        $business = auth()->user()->business;
        $plan = $business->plan;
        
        $usageBytes = $business->storageUsed();
        $limitGB = $plan->limit_storage ?? 0;
        $limitBytes = $limitGB * 1024 * 1024 * 1024;
        
        $percentage = $limitBytes > 0 ? min(100, ($usageBytes / $limitBytes) * 100) : 0;

        // Get recent media for management
        $userIds = $business->users()->pluck('id');
        $productIds = Product::where('business_id', $business->id)->pluck('id');

        $media = Media::where(function($query) use ($userIds, $productIds) {
            $query->where(function($q) use ($userIds) {
                $q->where('model_type', User::class)->whereIn('model_id', $userIds);
            })->orWhere(function($q) use ($productIds) {
                $q->where('model_type', Product::class)->whereIn('model_id', $productIds);
            });
        })->orderByDesc('created_at')->paginate(20);

        $logsCount = LoginLog::count(); // Scoped by BelongsToBusiness automatically

        return view('user::storage.index', compact(
            'business', 'plan', 'usageBytes', 'limitGB', 'percentage', 'media', 'logsCount'
        ));
    }

    public function deleteMedia(Media $media) {
        auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Business Owner']) || abort(403);
        
        // Safety check: Ensure media belongs to this business
        $business = auth()->user()->business;
        $isUserMedia = $media->model_type === User::class && User::where('id', $media->model_id)->where('business_id', $business->id)->exists();
        $isProductMedia = $media->model_type === Product::class && Product::where('id', $media->model_id)->where('business_id', $business->id)->exists();

        if (!$isUserMedia && !$isProductMedia && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized media deletion.');
        }

        $media->delete();

        toast('Media deleted successfully!', 'success');
        return redirect()->back();
    }

    public function clearLogs() {
        auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Business Owner']) || abort(403);
        
        // Scoped by BelongsToBusiness automatically
        LoginLog::query()->delete();

        toast('Login logs cleared!', 'success');
        return redirect()->back();
    }
}
