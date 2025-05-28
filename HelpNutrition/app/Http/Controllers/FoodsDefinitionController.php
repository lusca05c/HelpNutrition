<?php

namespace App\Http\Controllers;

use App\Models\FoodsDefinition;

class FoodsDefinitionController extends Controller
{
    /**
     * Exibir todos os dados de alimentos encontrados
     */
    public static function index()
    {      
        $foods = FoodsDefinition::all();
        return $foods;
    }

    // Função para retornar a comida com o nome mais próximo ao digitado pelo usuário

    public static function returnFoodUser(String $food)
    {
        return FoodsDefinition::where("descricacao_do_alimento", "like", $food . "%")->first();
    }

    // Função para verificar se os alimentos de fato existem
    public static function validateArgFood(String $mealComp)
    {
        $food = $mealComp;

        $food = preg_replace('/\s*\(.*?\)\s*/', '', $food);

        $food = FoodsDefinition::where("descricacao_do_alimento", "like", $food . "%")->first();

        if(!($food))
        {
            return 0;
        }else
        {
           return 1;
        }

    }

    // Função para formatar e validar a refeição descrita pelo o usuário

    public static function validateMeal(String &$meal, String $fconst)
    {

        $meal = trim($meal);
        $meal = ucfirst((strtolower($meal)));

        //Garantir que as vírgulas tenham um único espaço após elas
        $meal = preg_replace('/\s*,\s*/', ', ', $meal);

        // Dividir a string com base na vírgula
        $meal = explode(", ", $meal);

        //Remover espaços extras ao redor de cada alimento
        $meal = array_map('trim', $meal);


        foreach($meal as $aux)
        {
            if (!self::validateArgFood($aux)) {
                echo("O alimento '$aux' não é válido ou não foi encontrado.");
                return 0; // Interrompe a execução
            }
        }

        foreach($meal as $aux)
        {
            if($aux == $fconst){
                return 1;
            }
        }

        return 0; // Se meal estiver correto, mas não tiver a Food que deseja alterar, erro
                
    }

    /**
     *  Função para encontrar os dados de uma comida
     */

    public static function fatGroupSituation(FoodsDefinition $food)
    {
        $fatGroup = 0.0;

        foreach($food as $component => $value)
        {
            // Compara os primeiros 3 caracteres garantindo Ácidos Graxos ≃ Gorduras
            if (substr($component, 0, 3) === "AG_") {
                $fatGroup += (float)str_replace(',', '.', ($value));
            }
        }

            return $fatGroup;

    }

    /**
     * Função para encontrar um substituto adequado
     */

     public static function findSubstituteFood(FoodsDefinition $food)
     {
         $bestChoice = null;
         $foodsAll = self::index();
         $minDifference = PHP_FLOAT_MAX;

         foreach ($foodsAll as $foodValue) {
            if ($foodValue["Categoria"] == $food["Categoria"])
            {
                if($foodValue["descricacao_do_alimento"] !== $food["descricacao_do_alimento"])
                {
                 $kcalDifference = abs(((float) str_replace(',', '.', $foodValue["Energia_kcal"])) - ((float)str_replace(',', '.', $food["Energia_kcal"])));
                 $carbDifference = abs(((float) str_replace(',', '.', $foodValue["Carboi_drato_g"])) - ((float)str_replace(',', '.', $food["Carboi_drato_g"])));
                 $proteinDifference = abs(((float) str_replace(',' ,'.', $foodValue["Protein_g"])) - ((float)str_replace(',', '.', $food["Proteina_g"])));
                 $fatDifference = abs(self::fatGroupSituation($foodValue) - self::fatGroupSituation($food));
                
                 $totalDifference = $kcalDifference + $carbDifference + $proteinDifference + $fatDifference;
                 
                 
                 if ($totalDifference < $minDifference) {
                     $minDifference = $totalDifference;
                     $bestChoice = $foodValue;
                 }
                }
             }
         }
     
         
         if (!$bestChoice) {
             echo 'Nenhum substituto adequado foi encontrado.\n';
         }
     
         return $bestChoice;
     }
     

    /**
     * Função de substituição de alimento
    */

     public static function replaceFood(array $meal, FoodsDefinition $foodObject, String $food)
     {

        $substituteFood = self::findSubstituteFood($foodObject);

        if(!$substituteFood)
        {
            echo('Nenhum substituto adequado foi encontrado...');
        }

        foreach ($meal as $index => $item) {
            // Verifica se o alimento a ser substituído existe na refeição e substitui
            if ($item == $food) {
                $meal[$index] = str_replace($food, $substituteFood['descricacao_do_alimento'], $item);
            }
        }

        return $meal;

     }

}