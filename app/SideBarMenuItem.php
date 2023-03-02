<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\SideBarMenuItem
 *
 * @property mixed $id
 * @property string $title Название
 * @property string $route_name Имя маршрута
 * @property string $slug Короткое имя
 * @property string|null $access_permissions Права доступа для отображения
 * @property string|null $css_class Стили отображения
 * @property string $tooltip_prompt Всплывающая подсказка
 * @property int|null $parent_id Указатель на родительский элемент меню
 * @property int $is_header
 * @property string|null $icon_class Адрес запроса для получения информации о счетчике
 * @property string|null $webhook_variable Название переменной, содержащей значение счетчика
 * @property int $sort Сортировка меню
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|SideBarMenuItem[] $children
 * @property-read int|null $children_count
 * @property-read SideBarMenuItem|null $parent
 * @method static Builder|SideBarMenuItem newModelQuery()
 * @method static Builder|SideBarMenuItem newQuery()
 * @method static Builder|SideBarMenuItem query()
 * @method static Builder|SideBarMenuItem whereAccessPermissions($value)
 * @method static Builder|SideBarMenuItem whereCreatedAt($value)
 * @method static Builder|SideBarMenuItem whereCssClass($value)
 * @method static Builder|SideBarMenuItem whereIconClass($value)
 * @method static Builder|SideBarMenuItem whereId($value)
 * @method static Builder|SideBarMenuItem whereIsHeader($value)
 * @method static Builder|SideBarMenuItem whereParentId($value)
 * @method static Builder|SideBarMenuItem whereRouteName($value)
 * @method static Builder|SideBarMenuItem whereSlug($value)
 * @method static Builder|SideBarMenuItem whereSort($value)
 * @method static Builder|SideBarMenuItem whereTitle($value)
 * @method static Builder|SideBarMenuItem whereTooltipPrompt($value)
 * @method static Builder|SideBarMenuItem whereUpdatedAt($value)
 * @method static Builder|SideBarMenuItem whereWebhookVariable($value)
 * @mixin \Eloquent
 */
class SideBarMenuItem extends Model
{
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}