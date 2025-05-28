<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodsDefinition extends Model
{
    use HasFactory;

    protected $table = 'foods_definitions';

    protected $fillable = ['id', 'Codigo', 'descricacao_do_alimento', 'Categoria', 'descricao_da_preparacao',
    'Energia_kcal', 'Proteina_g', 'Carboi_drato_g', 'Fibra_alimentar_total_g', 'AG_Mono_g', 'AG_Poli_g',
    'AG_Lino_leico_g', 'AG_Linole_nico_g', 'AG_Trans_total_g'];

}
