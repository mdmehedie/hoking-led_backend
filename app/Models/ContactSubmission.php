<?php

namespace App\Models;

use App\Filament\Admin\Resources\CaseStudyResource;
use App\Filament\Admin\Resources\BlogResource;
use App\Filament\Admin\Resources\BrandResource;
use App\Filament\Admin\Resources\NewsResource;
use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\ProductResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'place',
        'subject',
        'message',
        'source',
        'status',
        'priority',
        'assigned_to',
        'admin_notes',
        'responded_at',
        'resolved_at',
        'auto_delete_at',
        'ip_address',
        'user_agent',
        'extras',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'auto_delete_at' => 'datetime',
        'extras' => 'array',
    ];

    // ─── Resource Type Mapping ─────────────────────────────────

    public const RESOURCE_TYPES = [
        'product'   => Product::class,
        'blog'      => Blog::class,
        'news'      => News::class,
        'project'   => Project::class,
        'page'      => Page::class,
        'case_study' => CaseStudy::class,
        'brand'     => Brand::class,
        'category'  => Category::class,
    ];

    public const RESOURCES = [
        'product'   => ProductResource::class,
        'blog' => BlogResource::class,
        'news' => NewsResource::class,
        'project' => ProductResource::class,
        'page' => PageResource::class,
        'case_study' => CaseStudyResource::class,
        'brand' => BrandResource::class,
        'category' => BrandResource::class,
    ];

    // ─── Polymorphic Resource Relationship ─────────────────────

    public function resource(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'extras', 'resource_type', 'resource_id');
    }

    public function getResourceTypeAttribute(): ?string
    {
        return $this->extras['resource_type'] ?? null;
    }

    public function getResourceIdAttribute(): ?int
    {
        $id = $this->extras['resource_id'] ?? null;
        return $id !== null ? (int) $id : null;
    }

    public function hasResource(): bool
    {
        return !empty($this->extras['resource_type']) && !empty($this->extras['resource_id']);
    }

    public function getResourceLabel(): ?string
    {
        if (!$this->hasResource()) {
            return null;
        }

        $modelClass = self::RESOURCE_TYPES[$this->extras['resource_type']] ?? null;
        if ($modelClass) {
            $resource = $modelClass::find($this->extras['resource_id']);
            if ($resource) {
                return match ($this->extras['resource_type']) {
                    'product'    => $resource->title ?? 'Product #' . $resource->id,
                    'blog'       => $resource->title ?? 'Blog #' . $resource->id,
                    'news'       => $resource->title ?? 'News #' . $resource->id,
                    'project'    => $resource->title ?? 'Project #' . $resource->id,
                    'page'       => $resource->title ?? 'Page #' . $resource->id,
                    'case_study' => $resource->title ?? 'Case Study #' . $resource->id,
                    'brand'      => $resource->name ?? 'Brand #' . $resource->id,
                    'category'   => $resource->name ?? 'Category #' . $resource->id,
                    default      => class_basename($resource) . ' #' . $resource->id,
                };
            }
        }

        return ($this->extras['resource_type'] ?? 'Unknown') . ' #' . $this->extras['resource_id'];
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopePendingResponse($query)
    {
        return $query->whereNull('responded_at')->where('status', '!=', 'closed');
    }

    public function scopeForResource($query, string $type, ?int $id = null)
    {
        return $query->where('extras->resource_type', $type)
            ->when($id !== null, fn ($q) => $q->where('extras->resource_id', $id));
    }

    public function scopeAwaitingSLA($query, int $hours = 24)
    {
        return $query->whereNull('responded_at')
            ->where('created_at', '<=', now()->subHours($hours));
    }

    // ─── Relationships ─────────────────────────────────────────

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ─── Status Methods ────────────────────────────────────────

    public function markAsInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markAsResponded(): void
    {
        $this->update([
            'status' => 'resolved',
            'responded_at' => now(),
        ]);
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);
    }

    public function assignTo($userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    // ─── Helper Methods ────────────────────────────────────────

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'contact_page' => 'Contact Page',
            'footer' => 'Footer',
            'popup' => 'Popup',
            'support_page' => 'Support Page',
            'product_page' => 'Product Page',
            'api' => 'API',
            'news_page' => 'News Page',
            'blog_page' => 'Blog Page',
            default => ucfirst(str_replace('_', ' ', $this->source)),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'success',
            'in_progress' => 'warning',
            'resolved' => 'info',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'gray',
        };
    }

    public function isOverdue(int $slaHours = 24): bool
    {
        return $this->responded_at === null
            && $this->created_at->diffInHours(now()) > $slaHours;
    }

    public function hoursSinceSubmission(): float
    {
        return $this->created_at->diffInHours(now(), true);
    }
}
