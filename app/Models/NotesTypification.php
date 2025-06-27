<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class NotesTypification extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'notes_typifications';

    protected $default = ['xid', 'name', 'parent_id', 'sale', 'schedule','status', 'x_parent_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id','parent_id'];

    protected $appends = ['xid','x_parent_id'];

    protected $filterable = ['id', 'name','parent_id', 'sale', 'schedule','status'];

    protected $hashableGetterFunctions = [
        'getXParentIdAttribute' => 'parent_id',
    ];

    protected $casts = [
        'parent_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);

        static::deleting(function (NotesTypification $child) {
            // 1) sólo si tiene padre
            if (! $child->parent_id) {
                return;
            }

            // buscamos al padre
            $parent = NotesTypification::find($child->parent_id);
            if (! $parent) {
                return;
            }

            // 2) si el padre ya tiene sale o schedule en true, no hacer nada
            if ($parent->sale || $parent->schedule) {
                return;
            }

            // 3) si el padre tiene otros hijos (aparte de éste), no hacemos la actualización
            $siblings = NotesTypification::where('parent_id', $child->parent_id)
                ->where('id', '<>', $child->id)
                ->exists();
            if ($siblings) {
                return;
            }

            // 4) si pasamos todas las validaciones, aplicamos los valores del hijo al padre
            $parent->update([
                'sale'     => $child->sale,
                'schedule' => $child->schedule,
            ]);
        });
    }

    public function subTypifications()
    {
        return $this->hasMany(NotesTypification::class, 'parent_id', 'id');
    }

    public function childs()
    {
        return $this->subcategories()->with('childs');
    }

}