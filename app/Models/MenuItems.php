<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItems extends Model
{
    protected $table = 'menu_items';
    protected $fillable = [
        'menu_id', 'page_id', 'title', 'slug', 'content',  'url', 'route', 'icon', 'parent_id', 'order', 'is_active'
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItems::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItems::class, 'parent_id')->orderBy('order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getUrlAttribute()
    {
        if ($this->route && Route::has($this->route)) {
            return route($this->route);
        }
        if ($this->page) {
            return route('page.show', $this->page->slug);
        }
        return $this->attributes['url'] ?? '#';
    }

    public static function updateOrder(array $orderData)
    {
        foreach ($orderData as $data) {
            self::where('id', $data['id'])->update([
                'order' => $data['order'],
                'parent_id' => $data['parent_id'] ?? null
            ]);
        }
    }
}
